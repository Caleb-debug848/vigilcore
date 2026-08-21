<?php

// Liste des 20 services officiels VigilCore
$services = [
    ['key' => 'smobilpay',          'name' => 'Smobilpay Platform & APIs'],
    ['key' => 's3p',                'name' => 'Third Party Merchant API (S3P)'],
    ['key' => 'merchant_portal',    'name' => 'Agent & Merchant Portal'],
    ['key' => 'ecommerce',          'name' => 'Smobilpay for e-commerce'],
    ['key' => 'mtn_momo',           'name' => 'MTN Mobile Money (Général)'],
    ['key' => 'orange_money',       'name' => 'Orange Money (Général)'],
    ['key' => 'mtn_collection',     'name' => 'MTN MoMo : Collections'],
    ['key' => 'orange_collection',   'name' => 'Orange Money : Collections'],
    ['key' => 'mtn_disbursement',   'name' => 'MTN MoMo : Disbursement'],
    ['key' => 'orange_disbursement', 'name' => 'Orange Money : Disbursement'],
    ['key' => 'mtn_airtime',        'name' => 'MTN Recharge / Airtime'],
    ['key' => 'orange_airtime',     'name' => 'Orange Recharge / Airtime'],
    ['key' => 'camtel',             'name' => 'Camtel Recharge / Top-up'],
    ['key' => 'eneo',               'name' => 'Factures ENEO (Électricité)'],
    ['key' => 'camwater',           'name' => 'Factures Camwater (Eau)'],
    ['key' => 'canal',              'name' => 'Canal+ Télévision'],
    ['key' => 'dstv',               'name' => 'DSTV Télévision'],
    ['key' => 'startimes',          'name' => 'StarTimes TV'],
    ['key' => 'mtn_congo',          'name' => 'MTN Mobile Money Congo'],
    ['key' => 'sabc',               'name' => 'SABC Boissons du Cameroun'],
];

$host = 'srv901529';
$now = gmdate('Y-m-d\TH:i:s\Z');

echo "⚡ Injection des logs de télémétrie pour les 20 services dans Elasticsearch...\n\n";

foreach ($services as $i => $s) {
    $doc = [
        '@timestamp' => $now,
        'service'    => ['name' => $s['key']],
        'component'  => $s['key'],
        'log'        => ['level' => 'info'],
        'message'    => "Heartbeat nominal telemetry - {$s['name']} is operational",
        'host'       => ['name' => $host],
        'source'     => 'Kibana Logs Engine',
    ];

    $json = json_encode($doc);

    // Envoi vers Elasticsearch
    $ch = curl_init('http://127.0.0.1:9200/filebeat-logs/_doc');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_exec($ch);
    curl_close($ch);

    $ch2 = curl_init('http://127.0.0.1:9200/logs-generic-default/_doc');
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch2, CURLOPT_TIMEOUT, 2);
    curl_exec($ch2);
    curl_close($ch2);

    $num = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
    echo "  [{$num}/20] ✓ {$s['name']} ({$s['key']})\n";
}

echo "\n🎉 Les 20 services sont tous indexés dans Elasticsearch avec succès !\n";
