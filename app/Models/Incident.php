<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $fillable = [
        'title',
        'description',
        'severity',
        'source',
        'status',
        'component',
        'alert_name',
        'message',
        'server',
        'is_resolved',
        'resolved_at',
        'statuspage_incident_id',
        'raw_payload'
    ];

    protected $attributes = [
        'source'      => 'Kibana Logs Engine',
        'status'      => 'open',
        'is_resolved' => false,
        'server'      => 'srv901529',
    ];

    // Conversion automatique des types
    protected $casts = [
        'raw_payload' => 'array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function setSeverityAttribute($value)
    {
        $this->attributes['severity'] = strtoupper($value ?? 'INFO');
    }

    public function getSeverityAttribute($value)
    {
        return strtoupper($value ?? 'INFO');
    }

    protected static function booted()
    {
        static::saving(function ($incident) {
            // Synchronisation bidirectionnelle title / alert_name
            if (empty($incident->title) && !empty($incident->alert_name)) {
                $incident->title = $incident->alert_name;
            } elseif (empty($incident->alert_name) && !empty($incident->title)) {
                $incident->alert_name = $incident->title;
            }

            // Synchronisation bidirectionnelle description / message
            if (empty($incident->description) && !empty($incident->message)) {
                $incident->description = $incident->message;
            } elseif (empty($incident->message) && !empty($incident->description)) {
                $incident->message = $incident->description;
            }

            // Verrouillage systématique de l'heure de résolution (resolved_at & is_resolved)
            if ($incident->status === 'resolved') {
                $incident->is_resolved = true;
                if (empty($incident->resolved_at)) {
                    $incident->resolved_at = now();
                }
            } else {
                $incident->is_resolved = false;
            }
        });
    }

    /**
     * Calcule le MTTR exact formatté (ex: 2m 14s ou 45s)
     */
    public function getMttrFormattedAttribute(): string
    {
        if ($this->status !== 'resolved') {
            return '--';
        }

        $start = $this->created_at;
        $end = $this->resolved_at ?? $this->updated_at;

        if (!$start || !$end) {
            return '--';
        }

        $diffSec = max(1, $start->diffInSeconds($end));
        if ($diffSec >= 60) {
            $mins = floor($diffSec / 60);
            $secs = $diffSec % 60;
            return "{$mins}m {$secs}s";
        }

        return "{$diffSec}s";
    }

    /**
     * Récupère le code d'erreur technique (ex: ERR_GATEWAY_TIMEOUT_504, 500)
     */
    public function getErrorCodeAttribute(): string
    {
        $raw = $this->raw_payload;
        if (is_array($raw)) {
            if (!empty($raw['error_code'])) return (string)$raw['error_code'];
            if (!empty($raw['http_status'])) return 'HTTP ' . $raw['http_status'];
            if (!empty($raw['status_code'])) return 'HTTP ' . $raw['status_code'];
        }
        return (strtoupper($this->severity) === 'CRITICAL') ? 'ERR_CRITICAL_500' : 'ERR_LATENCY_WARN';
    }

    /**
     * Récupère la cause racine identifiée
     */
    public function getRootCauseAttribute(): string
    {
        $raw = $this->raw_payload;
        if (is_array($raw) && !empty($raw['root_cause'])) {
            return (string)$raw['root_cause'];
        }
        return 'Délai d\'attente ou instabilité de liaison réseau détectée par les sondes de surveillance.';
    }

    /**
     * Récupère la note de remédiation / clôture
     */
    public function getResolutionNoteAttribute(): string
    {
        $raw = $this->raw_payload;
        if (is_array($raw) && !empty($raw['resolution_note'])) {
            return (string)$raw['resolution_note'];
        }
        if (is_array($raw) && !empty($raw['message_resolved'])) {
            return (string)$raw['message_resolved'];
        }
        return ($this->status === 'resolved') 
            ? 'Service rétabli, tests de validation concluants et retour à la nominale.' 
            : 'Investigation et rétablissement en cours par les équipes opérationnelles.';
    }

    /**
     * Récupère les endpoints API impactés sous forme de chaîne lisible
     */
    public function getAffectedEndpointsListAttribute(): string
    {
        $raw = $this->raw_payload;
        if (is_array($raw) && !empty($raw['affected_endpoints'])) {
            if (is_array($raw['affected_endpoints'])) {
                return implode(', ', $raw['affected_endpoints']);
            }
            return (string)$raw['affected_endpoints'];
        }
        $comp = $this->component ?? 'service';
        return "/api/v2/{$comp}/validate";
    }

    /**
     * Horodatage de déclenchement au fuseau horaire de Douala
     */
    public function getTriggeredAtWatAttribute(): string
    {
        return $this->created_at 
            ? $this->created_at->timezone('Africa/Douala')->format('d/m/Y H:i:s') . ' (WAT)'
            : 'N/A';
    }

    /**
     * Horodatage de résolution au fuseau horaire de Douala
     */
    public function getResolvedAtWatAttribute(): string
    {
        $end = $this->resolved_at ?? (($this->status === 'resolved') ? $this->updated_at : null);
        return $end 
            ? $end->timezone('Africa/Douala')->format('d/m/Y H:i:s') . ' (WAT)'
            : '--';
    }
}