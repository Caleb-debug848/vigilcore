<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function store(Request $request)
    {
        // Extraction flexible des champs (gère alert_name/title et message/description)
        $source      = $request->input('source', 'VigilCore Monitor');
        $title       = $request->input('title') ?? $request->input('alert_name', 'Incident non spécifié');
        $description = $request->input('description') ?? $request->input('message');
        $severity    = strtolower($request->input('severity', 'info'));
        $status      = strtolower($request->input('status', 'open'));

        // Création en base de données
        $incident = Incident::create([
            'source'      => $source,
            'title'       => $title,
            'description' => $description,
            'severity'    => $severity,
            'status'      => $status,
            'raw_payload' => $request->all(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Incident enregistré dans VigilCore',
            'id'      => $incident->id,
        ], 201);
    }
}