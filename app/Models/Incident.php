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
        });
    }
}