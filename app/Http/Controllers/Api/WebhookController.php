<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function store(Request $request)
    {
        // 1. Vérification de la clé secrète dans les headers
        $providedSecret = $request->header('X-Webhook-Secret');
        $expectedSecret = env('WEBHOOK_SECRET');

        // hash_equals protège contre les attaques temporelles
        if (!$providedSecret || !hash_equals($expectedSecret, $providedSecret)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid or missing webhook token.'
            ], 401);
        }

        // 2. Validation des données du payload
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'severity' => 'required|in:critical,warning,info',
        ]);

        // 3. Enregistrement de l'incident
        $incident = Incident::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'severity' => $validated['severity'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Incident securely logged into VigilCore.',
            'data' => $incident
        ], 201);
    }
}