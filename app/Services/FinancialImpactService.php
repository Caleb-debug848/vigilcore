<?php

namespace App\Services;

use App\Models\Incident;
use Carbon\Carbon;

class FinancialImpactService
{
    /**
     * Profils transactionnels et financiers par service (Écosystème Maviance / Smobilpay)
     */
    protected static array $serviceProfiles = [
        'eneo' => [
            'name'           => 'Factures ENEO (Électricité / Tokens)',
            'tpm'            => 120,    // 120 factures/min en moyenne
            'avg_amount_cfa' => 12500,  // Panier moyen facture électricité
            'commission_cfa' => 100,    // Commission moyenne par facture
        ],
        'camwater' => [
            'name'           => 'Factures Camwater (Eau & Assainissement)',
            'tpm'            => 50,     // 50 factures/min
            'avg_amount_cfa' => 8500,   // Panier moyen eau
            'commission_cfa' => 85,
        ],
        's3p' => [
            'name'           => 'Third Party Merchant API (S3P)',
            'tpm'            => 200,    // 200 appels API marchands/min
            'avg_amount_cfa' => 15000,
            'commission_cfa' => 150,
        ],
        'smobilpay' => [
            'name'           => 'Smobilpay Platform & APIs',
            'tpm'            => 220,
            'avg_amount_cfa' => 14000,
            'commission_cfa' => 140,
        ],
        'merchant_portal' => [
            'name'           => 'Agent & Merchant Portal',
            'tpm'            => 80,
            'avg_amount_cfa' => 10000,
            'commission_cfa' => 100,
        ],
        'mtn_momo' => [
            'name'           => 'MTN Mobile Money (Général / Collections)',
            'tpm'            => 250,    // Gros volume télécom
            'avg_amount_cfa' => 6500,
            'commission_cfa' => 65,
        ],
        'orange_money' => [
            'name'           => 'Orange Money (Général / Collections)',
            'tpm'            => 220,
            'avg_amount_cfa' => 7000,
            'commission_cfa' => 70,
        ],
        'camtel' => [
            'name'           => 'Camtel Recharge / Top-up',
            'tpm'            => 60,
            'avg_amount_cfa' => 5000,
            'commission_cfa' => 50,
        ],
        'canal' => [
            'name'           => 'Canal+ Télévision',
            'tpm'            => 45,
            'avg_amount_cfa' => 12000,
            'commission_cfa' => 120,
        ],
        'dstv' => [
            'name'           => 'DSTV Télévision',
            'tpm'            => 30,
            'avg_amount_cfa' => 16000,
            'commission_cfa' => 160,
        ],
        'startimes' => [
            'name'           => 'StarTimes TV',
            'tpm'            => 35,
            'avg_amount_cfa' => 7500,
            'commission_cfa' => 75,
        ],
        'sabc' => [
            'name'           => 'Boissons du Cameroun (SABC Payment)',
            'tpm'            => 40,
            'avg_amount_cfa' => 25000,
            'commission_cfa' => 250,
        ],
        'default' => [
            'name'           => 'Service Partenaire Smobilpay',
            'tpm'            => 60,
            'avg_amount_cfa' => 9000,
            'commission_cfa' => 80,
        ],
    ];

    /**
     * Détecte le profil financier adapté à un incident
     */
    public static function getProfile(Incident $incident): array
    {
        $comp = strtolower($incident->component ?? '');
        $title = strtolower($incident->title ?? '');

        foreach (self::$serviceProfiles as $key => $prof) {
            if ($key === 'default') continue;
            if (str_contains($comp, $key) || str_contains($title, $key)) {
                return $prof;
            }
        }

        // Détection par mot-clé métier
        if (str_contains($comp, 'eneo') || str_contains($title, 'eneo')) return self::$serviceProfiles['eneo'];
        if (str_contains($comp, 'camwater') || str_contains($title, 'camwater')) return self::$serviceProfiles['camwater'];
        if (str_contains($comp, 's3p') || str_contains($title, 's3p')) return self::$serviceProfiles['s3p'];
        if (str_contains($comp, 'mtn') || str_contains($title, 'mtn')) return self::$serviceProfiles['mtn_momo'];
        if (str_contains($comp, 'orange') || str_contains($title, 'orange')) return self::$serviceProfiles['orange_money'];
        if (str_contains($comp, 'camtel') || str_contains($title, 'camtel')) return self::$serviceProfiles['camtel'];
        if (str_contains($comp, 'canal') || str_contains($title, 'canal')) return self::$serviceProfiles['canal'];

        return self::$serviceProfiles['default'];
    }

    /**
     * Calcule l'impact financier précis d'un incident
     */
    public static function calculate(Incident $incident): array
    {
        $profile = self::getProfile($incident);

        // 1. Calcul de la durée en secondes
        try {
            $start = Carbon::parse($incident->created_at ?? now());
        } catch (\Throwable $e) {
            $start = now();
        }

        try {
            $end = ($incident->status === 'resolved')
                ? Carbon::parse($incident->resolved_at ?? $incident->updated_at ?? now())
                : now();
        } catch (\Throwable $e) {
            $end = now();
        }

        $diffSec = max(10, abs($start->diffInSeconds($end)));
        $diffMin = max(0.2, $diffSec / 60);

        // 2. Calcul des métriques financières
        $blockedTx = (int) ceil($diffMin * $profile['tpm']);
        $grossVolume = $blockedTx * $profile['avg_amount_cfa'];
        $commissionLoss = $blockedTx * $profile['commission_cfa'];

        $isResolved = ($incident->status === 'resolved');

        return [
            'is_resolved'               => $isResolved,
            'duration_sec'              => $diffSec,
            'duration_min'              => round($diffMin, 1),
            'duration_formatted'        => ($diffSec >= 60) ? (floor($diffSec / 60) . 'm ' . ($diffSec % 60) . 's') : ($diffSec . 's'),
            'tpm'                       => $profile['tpm'],
            'blocked_transactions'      => $blockedTx,
            'blocked_transactions_fmt'  => number_format($blockedTx, 0, ',', ' '),
            'avg_amount_cfa'            => $profile['avg_amount_cfa'],
            'gross_volume_cfa'          => $grossVolume,
            'gross_volume_fmt'          => number_format($grossVolume, 0, ',', ' ') . ' FCFA',
            'commission_loss_cfa'       => $commissionLoss,
            'commission_loss_fmt'       => number_format($commissionLoss, 0, ',', ' ') . ' FCFA',
            'status_badge_text'         => $isResolved 
                ? ('🛡️ ' . number_format($grossVolume, 0, ',', ' ') . ' FCFA préservés')
                : ('⚡ ' . number_format($grossVolume, 0, ',', ' ') . ' FCFA en risque'),
            'commission_badge_text'     => $isResolved 
                ? ('+' . number_format($commissionLoss, 0, ',', ' ') . ' FCFA sécurisés')
                : ('-' . number_format($commissionLoss, 0, ',', ' ') . ' FCFA en risque'),
        ];
    }
}
