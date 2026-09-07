<?php
// Script autonome et infaillible de scan WhatsApp VigilCore
header('Content-Type: text/html; charset=utf-8');

$apiKey = trim(@shell_exec('docker exec vigilcore-whatsapp printenv AUTHENTICATION_API_KEY 2>/dev/null') ?: 'B6D711FCDE4D4FD5936544120E713976');

function httpGet($url, $key) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["apikey: $key"]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$stateData = httpGet('http://127.0.0.1:8090/instance/connectionState/vigilcore-ops', $apiKey);
$isConnected = isset($stateData['instance']['state']) && $stateData['instance']['state'] === 'open';

$base64 = '';
$pairingCode = '';
$code = '';

if (!$isConnected) {
    $connectData = httpGet('http://127.0.0.1:8090/instance/connect/vigilcore-ops', $apiKey);
    $base64 = $connectData['base64'] ?? '';
    $code = $connectData['code'] ?? '';
    $pairingCode = $connectData['pairingCode'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp QR Connect - VigilCore</title>
    <?php if (!$isConnected): ?>
    <meta http-equiv="refresh" content="20">
    <?php endif; ?>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #0c1317;
            color: #e9edef;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .card {
            background: #111b21;
            padding: 32px 24px;
            border-radius: 16px;
            text-align: center;
            max-width: 440px;
            width: 100%;
            border: 1px solid #222e35;
            box-shadow: 0 10px 30px rgba(0,0,0,0.8);
        }
        h1 {
            color: #00a884;
            font-size: 22px;
            margin-top: 0;
            margin-bottom: 8px;
        }
        .qr-frame {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            display: inline-block;
            margin: 20px auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .qr-frame img {
            display: block;
            max-width: 300px;
            width: 100%;
            height: auto;
            margin: 0 auto;
        }
        .connected-banner {
            background: #00a88422;
            border: 2px solid #00a884;
            border-radius: 12px;
            padding: 28px 20px;
            margin: 20px 0;
        }
        .steps {
            background: #202c33;
            border-radius: 10px;
            padding: 14px 18px;
            text-align: left;
            font-size: 14px;
            color: #d1d7db;
            line-height: 1.6;
        }
        .steps ol {
            margin: 0;
            padding-left: 20px;
        }
        .badge {
            background: #00a884;
            color: #111b21;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
        }
        .pulse {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ffb703;
            margin-right: 6px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Connecter VigilCore WhatsApp</h1>
        
        <?php if ($isConnected): ?>
            <div class="connected-banner">
                <div style="font-size: 52px; color: #00a884; margin-bottom: 12px;">✓</div>
                <h2 style="color: #00a884; margin: 0 0 8px 0; font-size: 20px;">WhatsApp Connecté !</h2>
                <p style="color: #e9edef; font-size: 14px; margin: 0;">L'instance est active et synchronisée. Vous pouvez exécuter <code>./demo.sh</code>.</p>
            </div>
        <?php else: ?>
            <p style="color: #8696a0; font-size: 14px; margin: 0;">
                <span class="pulse"></span> Actualisation automatique toutes les 8s
            </p>

            <div class="qr-frame">
                <?php if (!empty($base64)): ?>
                    <img src="<?= htmlspecialchars($base64) ?>" alt="WhatsApp QR Code">
                <?php elseif (!empty($pairingCode)): ?>
                    <div style="width: 260px; height: 260px; display: flex; flex-direction: column; justify-content: center; align-items: center; color: #111b21;">
                        <p style="font-size: 12px; margin: 0; color: #666;">Code d'appairage :</p>
                        <h2 style="font-size: 28px; margin: 8px 0; color: #00a884; letter-spacing: 2px;"><?= htmlspecialchars($pairingCode) ?></h2>
                    </div>
                <?php else: ?>
                    <div style="width: 260px; height: 260px; display: flex; justify-content: center; align-items: center; color: #111b21; font-size: 14px;">
                        Génération du QR Code en cours...
                    </div>
                <?php endif; ?>
            </div>

            <div class="steps">
                <ol>
                    <li>Ouvrez <strong>WhatsApp</strong> sur votre téléphone.</li>
                    <li>Appuyez sur <span class="badge">⋮</span> ou <strong>Réglages</strong>.</li>
                    <li>Allez dans <strong>Appareils connectés</strong> $\rightarrow$ <strong>Connecter un appareil</strong>.</li>
                    <li>Scannez le QR Code ci-dessus.</li>
                </ol>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
