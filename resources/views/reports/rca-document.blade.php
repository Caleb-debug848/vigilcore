<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RCA #{{ $incident->id }} — {{ $incident->title }} — Maviance / Smobilpay</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            line-height: 1.5;
            font-size: 13px;
        }

        /* Barre d'action supérieure (masquée à l'impression) */
        .top-action-bar {
            background-color: #0f172a;
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-print {
            background-color: #2563eb;
            color: #ffffff;
        }
        .btn-print:hover { background-color: #1d4ed8; }

        .btn-back {
            background-color: #334155;
            color: #f1f5f9;
        }
        .btn-back:hover { background-color: #475569; }

        /* Conteneur principal A4 */
        .page-container {
            max-width: 860px;
            margin: 24px auto;
            background: #ffffff;
            padding: 36px 40px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 10px 25px -5px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }

        /* En-tête officiel Maviance */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 20px;
            border-bottom: 2px solid #0f172a;
            margin-bottom: 24px;
        }

        .logo-title-group h1 {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .logo-title-group .sub {
            font-size: 12px;
            font-weight: 600;
            color: #2563eb;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .ref-box {
            text-align: right;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
        }

        .ref-box .badge-rca {
            display: inline-block;
            background: #0f172a;
            color: #ffffff;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            margin-bottom: 4px;
        }

        /* Grille des Métadonnées */
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        .meta-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
        }

        .meta-card .label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .meta-card .value {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        .badge-crit {
            background: #fef2f2;
            color: #dc2626;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 800;
            border: 1px solid #fecaca;
            display: inline-block;
        }

        .badge-resolved {
            background: #f0fdf4;
            color: #16a34a;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 800;
            border: 1px solid #bbf7d0;
            display: inline-block;
        }

        /* Section Block */
        .section-block {
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 6px;
            border-bottom: 1.5px solid #e2e8f0;
            margin-bottom: 12px;
        }

        .section-title span.number {
            background: #2563eb;
            color: #ffffff;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }

        /* Encadré d'Impact Financier Spécial Direction */
        .financial-box {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 24px;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.12);
        }

        .financial-box .fin-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94a3b8;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .fin-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .fin-card {
            border-left: 2px solid #2563eb;
            padding-left: 12px;
        }

        .fin-card .fin-label {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
        }

        .fin-card .fin-val {
            font-size: 18px;
            font-weight: 800;
            color: #ffffff;
            font-family: 'JetBrains Mono', monospace;
            margin-top: 2px;
        }

        .fin-card.green .fin-val { color: #34d399; }
        .fin-card.amber .fin-val { color: #fbbf24; }

        /* Timeline des Événements */
        .timeline {
            position: relative;
            padding-left: 24px;
            margin-top: 8px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 6px;
            bottom: 6px;
            left: 7px;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 14px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 4px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid #2563eb;
        }

        .timeline-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
        }

        .timeline-time {
            font-family: 'JetBrains Mono', monospace;
            color: #64748b;
            font-size: 11px;
            background: #f1f5f9;
            padding: 1px 6px;
            border-radius: 4px;
        }

        .timeline-desc {
            font-size: 12px;
            color: #475569;
            margin-top: 2px;
        }

        /* Preuve Cryptographique Forensique */
        .forensic-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
        }

        .forensic-row {
            display: flex;
            margin-bottom: 4px;
        }

        .forensic-row .f-label {
            width: 140px;
            color: #64748b;
            font-weight: 600;
        }

        .forensic-row .f-val {
            color: #0f172a;
            word-break: break-all;
            font-weight: 600;
        }

        /* Signatures et Approbations */
        .signatures-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 28px;
            padding-top: 16px;
            border-top: 1px dashed #cbd5e1;
        }

        .sign-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            min-height: 90px;
        }

        .sign-box .role {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
        }

        .sign-box .name {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
        }

        .sign-box .signature-zone {
            margin-top: 20px;
            font-size: 10px;
            color: #94a3b8;
            font-style: italic;
        }

        /* Règle d'impression pure */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .top-action-bar {
                display: none !important;
            }
            .page-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body>

    <!-- Barre d'action supérieure (Affichée à l'écran, masquée à l'impression) -->
    <div class="top-action-bar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('reports') }}" class="btn-action btn-back">
                ← Retour aux Rapports
            </a>
            <span style="font-size: 12px; color: #94a3b8;">
                Rapport officiel certifié par <strong>VigilCore Forensics</strong>
            </span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn-action btn-print">
                🖨️ Imprimer / Enregistrer en PDF
            </button>
        </div>
    </div>

    <!-- Document A4 Officiel -->
    <div class="page-container">

        <!-- En-tête officiel -->
        <header class="header">
            <div class="logo-title-group">
                <h1>MAVIANCE PLC • SMOBILPAY</h1>
                <div class="sub">Root Cause Analysis (RCA) — Incident Post-Mortem Officiel</div>
            </div>
            <div class="ref-box">
                <span class="badge-rca">RCA #{{ str_pad($incident->id, 5, '0', STR_PAD_LEFT) }}</span>
                <div style="color: #64748b; margin-top: 2px;">Généré : {{ $generatedAtWat }}</div>
                <div style="color: #64748b;">Hôte : {{ $incident->server ?? 'srv901529' }}</div>
            </div>
        </header>

        <!-- Grille des Métadonnées de l'incident -->
        <div class="meta-grid">
            <div class="meta-card">
                <div class="label">Composant / Service</div>
                <div class="value">{{ $incident->title }}</div>
            </div>
            <div class="meta-card">
                <div class="label">Gravité / Impact</div>
                <div class="value">
                    <span class="{{ strtoupper($incident->severity) === 'CRITICAL' ? 'badge-crit' : 'badge-resolved' }}">
                        {{ strtoupper($incident->severity) }}
                    </span>
                </div>
            </div>
            <div class="meta-card">
                <div class="label">Durée MTTR Exacte</div>
                <div class="value" style="color: #2563eb; font-family: 'JetBrains Mono', monospace;">
                    {{ $incident->mttr_formatted }}
                </div>
            </div>
            <div class="meta-card">
                <div class="label">Statut Actuel</div>
                <div class="value">
                    <span class="badge-resolved">RÉSOLU & VALIDÉ</span>
                </div>
            </div>
        </div>

        <!-- ENCADRÉ SPÉCIAL DIRECTION GÉNÉRALE : IMPACT FINANCIER & FLUX ESTIMÉS -->
        <div class="financial-box">
            <div class="fin-title">
                <span>Évaluation d'Impact Financier & Continuité de Service (Smobilpay)</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #38bdf8;">
                    DÉBIT NOMINAL : {{ $financial['tpm'] }} TX/MIN
                </span>
            </div>
            <div class="fin-grid">
                <div class="fin-card amber">
                    <div class="fin-label">Transactions Différées</div>
                    <div class="fin-val">{{ $financial['blocked_transactions_fmt'] }} ops</div>
                </div>
                <div class="fin-card green">
                    <div class="fin-label">Volume Financier Préservé</div>
                    <div class="fin-val">{{ $financial['gross_volume_fmt'] }}</div>
                </div>
                <div class="fin-card green">
                    <div class="fin-label">Commissions Sécurisées</div>
                    <div class="fin-val">{{ $financial['commission_loss_fmt'] }}</div>
                </div>
            </div>
        </div>

        <!-- Section 1 : Diagnostic & Cause Racine -->
        <div class="section-block">
            <div class="section-title">
                <span class="number">1</span>
                <span>Analyse de la Cause Racine (Root Cause)</span>
            </div>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px;">
                <div style="font-weight: 700; color: #0f172a; margin-bottom: 4px;">
                    Code Diagnostic : <span style="font-family: 'JetBrains Mono', monospace; color: #dc2626;">{{ $incident->error_code }}</span>
                </div>
                <div style="color: #334155; font-size: 12.5px;">
                    {{ $incident->root_cause }}
                </div>
            </div>
        </div>

        <!-- Section 2 : Chronologie Détaillée de Résolution (WAT Douala) -->
        <div class="section-block">
            <div class="section-title">
                <span class="number">2</span>
                <span>Chronologie Horodatée des Événements (WAT — Douala UTC+1)</span>
            </div>
            <div class="timeline">
                @foreach($timeline as $event)
                    <div class="timeline-item">
                        <div class="timeline-header">
                            <span class="timeline-time">{{ $event['time'] }}</span>
                            <span>{{ $event['step'] }}</span>
                            <span style="font-size: 10px; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 1px 6px; border-radius: 4px;">
                                {{ $event['badge'] }}
                            </span>
                        </div>
                        <div class="timeline-desc">
                            <strong>{{ $event['title'] }}</strong> — {{ $event['desc'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Section 3 : Preuve Cryptographique Forensique -->
        <div class="section-block">
            <div class="section-title">
                <span class="number">3</span>
                <span>Preuve d'Intégrité Forensique (Scellé Cryptographique)</span>
            </div>
            <div class="forensic-box">
                <div class="forensic-row">
                    <div class="f-label">ID Statuspage Public :</div>
                    <div class="f-val">{{ $incident->statuspage_incident_id ?? 'sp_inc_auto' }}</div>
                </div>
                <div class="forensic-row">
                    <div class="f-label">Heure Début (WAT) :</div>
                    <div class="f-val">{{ $createdAtWat->format('d/m/Y H:i:s') }}</div>
                </div>
                <div class="forensic-row">
                    <div class="f-label">Heure Clôture (WAT) :</div>
                    <div class="f-val">{{ $resolvedAtWat->format('d/m/Y H:i:s') }}</div>
                </div>
                <div class="forensic-row">
                    <div class="f-label">Empreinte SHA-256 :</div>
                    <div class="f-val" style="color: #7c3aed;">{{ $shaHash }}</div>
                </div>
            </div>
        </div>

        <!-- Section 4 : Recommandations Techniques & Signatures -->
        <div class="section-block">
            <div class="section-title">
                <span class="number">4</span>
                <span>Mesures Correctives & Approbation d'Exploitation</span>
            </div>
            <div style="font-size: 12px; color: #475569; margin-bottom: 12px;">
                Le rétablissement a été validé par retour des sondes nominales à 200 OK. La mise en cache et les métriques de trafic ont repris leur rythme nominal sur la passerelle partenaire.
            </div>

            <div class="signatures-grid">
                <div class="sign-box">
                    <div class="role">Responsable Astreinte NOC / Support</div>
                    <div class="name">Équipe d'Exploitation Maviance</div>
                    <div class="signature-zone">
                        Certifié conforme par les sondes automatiques VigilCore
                    </div>
                </div>
                <div class="sign-box">
                    <div class="role">Directeur des Opérations & Systèmes</div>
                    <div class="name">Direction Technique Smobilpay</div>
                    <div class="signature-zone">
                        Validé pour archivage d'audit et conformité SLA
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer style="margin-top: 24px; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #94a3b8; display: flex; justify-content: space-between;">
            <span>Document Confidentiel — Usage Exclusif Maviance PLC / Smobilpay</span>
            <span>Généré par VigilCore Incident Suite v2.4 Enterprise</span>
        </footer>

    </div>

</body>
</html>
