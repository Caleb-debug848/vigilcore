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
        'statuspage_incident_id',
        'raw_payload'
    ];

    protected $attributes = [
        'source' => 'n8n Webhook',
        'status' => 'open',
    ];

    // Conversion automatique du champ JSON
    protected $casts = [
        'raw_payload' => 'array',
    ];
}