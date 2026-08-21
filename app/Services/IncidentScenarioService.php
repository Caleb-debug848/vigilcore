<?php

namespace App\Services;

use App\Models\Incident;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IncidentScenarioService
{
    /**
     * Retourne la liste complète des 20 scénarios organisés par catégorie
     */
    public static function getScenarios(): array
    {
        return [
            // --- 1. PLATEFORMES MAVIANCE ---
            [
                'key'        => 'smobilpay',
                'category'   => 'maviance',
                'category_label' => 'Plateformes Maviance',
                'name'       => 'Smobilpay Platform & APIs',
                'alert_title'=> 'Perturbation Plateforme Smobilpay',
                'severity'   => 'CRITICAL',
                'icon'       => 'credit-card',
                'color'      => 'indigo',
                'messages'   => [
                    'investigating' => 'Chers partenaires, nous constatons actuellement des ralentissements inhabituels lors de la validation de certains paiements sur la plateforme Smobilpay. Nos équipes techniques sont immédiatement mobilisées pour analyser la situation et rétablir la fluidité du service. Nous nous excusons sincèrement pour la gêne occasionnée dans vos activités.',
                    'identified'    => 'Chers partenaires, la cause du ralentissement a été localisée avec précision. Nos ingénieurs finalisent le correctif afin de relancer le traitement normal des transactions en toute sécurité. Vos flux restent protégés.',
                    'monitoring'    => 'Les transactions s\'exécutent de nouveau avec succès sur l\'ensemble de la plateforme. Nos équipes restent en veille active afin de s\'assurer d\'une parfaite stabilité.',
                    'resolved'      => 'Le service global Smobilpay est désormais 100 % opérationnel et fluide. Nous vous remercions chaleureusement pour votre patience et votre confiance continue.'
                ]
            ],
            [
                'key'        => 's3p',
                'category'   => 'maviance',
                'category_label' => 'Plateformes Maviance',
                'name'       => 'Third Party Merchant API (S3P)',
                'alert_title'=> 'Instabilité API Marchand S3P',
                'severity'   => 'CRITICAL',
                'icon'       => 'code-bracket',
                'color'      => 'purple',
                'messages'   => [
                    'investigating' => 'Chers intégrateurs et marchands, des délais de réponse allongés sont actuellement relevés sur les points d\'accès de l\'API S3P. Nos équipes d\'astreinte sont à pied d\'œuvre pour identifier la source du blocage et rétablir les échanges instantanés.',
                    'identified'    => 'La cause du blocage sur la passerelle d\'échange a été identifiée. Une mesure corrective est en cours d\'application pour restaurer un temps de réponse optimal.',
                    'monitoring'    => 'Les appels API et les validations de paiement marchand reprennent à un rythme normal. Nous surveillons attentivement les métriques de trafic.',
                    'resolved'      => 'L\'API S3P fonctionne de nouveau de manière optimale pour l\'ensemble de vos intégrations. Merci pour votre compréhension.'
                ]
            ],
            [
                'key'        => 'merchant_portal',
                'category'   => 'maviance',
                'category_label' => 'Plateformes Maviance',
                'name'       => 'Agent & Merchant Portal',
                'alert_title'=> 'Lenteurs Portail Marchand',
                'severity'   => 'CRITICAL',
                'icon'       => 'building-storefront',
                'color'      => 'blue',
                'messages'   => [
                    'investigating' => 'Chers agents et marchands, l\'accès à votre espace de gestion et le chargement des historiques connaissent des lenteurs temporaires. Rassurez-vous, vos données et soldes sont parfaitement sécurisés. Nous vérifions la situation sans délai.',
                    'identified'    => 'L\'origine des lenteurs sur le portail a été détectée. Le rétablissement complet des accès est en cours de finalisation par nos équipes.',
                    'monitoring'    => 'La connexion et la navigation sur votre portail sont de nouveau stables. Nous veillons au bon déroulement de chaque opération.',
                    'resolved'      => 'Votre portail agent et marchand est pleinement accessible et opérationnel. Bonne suite dans vos opérations quotidiennes !'
                ]
            ],
            [
                'key'        => 'ecommerce',
                'category'   => 'maviance',
                'category_label' => 'Plateformes Maviance',
                'name'       => 'Smobilpay for e-commerce',
                'alert_title'=> 'Perturbation Module E-Commerce',
                'severity'   => 'CRITICAL',
                'icon'       => 'shopping-bag',
                'color'      => 'cyan',
                'messages'   => [
                    'investigating' => 'Chers partenaires e-commerce, des échecs intermittents sont observés lors de la finalisation des paiements en ligne sur vos boutiques. Nous intervenons en priorité pour vous permettre d\'encaisser vos clients sereinement.',
                    'identified'    => 'Le dysfonctionnement affectant le module de paiement en ligne a été identifié. La mise à jour corrective est en cours de déploiement.',
                    'monitoring'    => 'Les paiements sur les boutiques partenaires s\'effectuent de nouveau avec succès. Nous surveillons le bon acheminement des commandes.',
                    'resolved'      => 'Le module e-commerce fonctionne parfaitement. Vos clients peuvent finaliser leurs achats en toute tranquillité.'
                ]
            ],

            // --- 2. MOBILE MONEY & TÉLÉCOMS (CAMEROUN) ---
            [
                'key'        => 'mtn_momo',
                'category'   => 'momo',
                'category_label' => 'Mobile Money & Télécoms',
                'name'       => 'MTN Mobile Money (Général)',
                'alert_title'=> 'Perturbation Réseau MTN MoMo',
                'severity'   => 'CRITICAL',
                'icon'       => 'device-phone-mobile',
                'color'      => 'amber',
                'messages'   => [
                    'investigating' => 'Chers clients et partenaires, des instabilités temporaires touchent actuellement les opérations MTN Mobile Money. Nos équipes collaborent activement avec l\'opérateur pour un retour rapide à la normale.',
                    'identified'    => 'La liaison technique avec l\'opérateur MTN a été localisée comme source du ralentissement. Le rétablissement de la communication est en cours.',
                    'monitoring'    => 'Les transactions MTN MoMo reprennent progressivement. Nous nous assurons que chaque transfert en attente aboutisse correctement.',
                    'resolved'      => 'L\'ensemble des services MTN Mobile Money est totalement rétabli. Merci pour votre patience.'
                ]
            ],
            [
                'key'        => 'orange_money',
                'category'   => 'momo',
                'category_label' => 'Mobile Money & Télécoms',
                'name'       => 'Orange Money (Général)',
                'alert_title'=> 'Perturbation Réseau Orange Money',
                'severity'   => 'CRITICAL',
                'icon'       => 'device-phone-mobile',
                'color'      => 'orange',
                'messages'   => [
                    'investigating' => 'Chers utilisateurs, des lenteurs sont actuellement signalées sur le réseau Orange Money. Nous menons les vérifications nécessaires avec les équipes de l\'opérateur pour sécuriser vos opérations.',
                    'identified'    => 'Le point d\'instabilité sur le canal Orange Money a été trouvé. Les réglages nécessaires sont en cours d\'application.',
                    'monitoring'    => 'Les transactions Orange Money sont de nouveau traitées avec succès. Nous gardons une observation étroite sur les validations.',
                    'resolved'      => 'Le service Orange Money est de nouveau 100 % disponible et stable.'
                ]
            ],
            [
                'key'        => 'mtn_collection',
                'category'   => 'momo',
                'category_label' => 'Mobile Money & Télécoms',
                'name'       => 'MTN MoMo : Collections (Encaissements)',
                'alert_title'=> 'Échecs Encaissements MTN MoMo',
                'severity'   => 'CRITICAL',
                'icon'       => 'arrow-down-tray',
                'color'      => 'yellow',
                'messages'   => [
                    'investigating' => 'Chers marchands, les encaissements via MTN MoMo rencontrent des rejets temporaires. Soyez assurés que nous traitons cette anomalie en priorité pour vos encaissements clients.',
                    'identified'    => 'La cause des rejets d\'encaissements MTN a été diagnostiquée. Le canal de validation est en cours de redémarrage.',
                    'monitoring'    => 'Les encaissements MTN MoMo sont de nouveau acceptés et validés. Nous vérifions le bon crédit de vos comptes.',
                    'resolved'      => 'Le service d\'encaissement MTN Mobile Money fonctionne à nouveau sans la moindre interruption.'
                ]
            ],
            [
                'key'        => 'orange_collection',
                'category'   => 'momo',
                'category_label' => 'Mobile Money & Télécoms',
                'name'       => 'Orange Money : Collections',
                'alert_title'=> 'Échecs Encaissements Orange Money',
                'severity'   => 'CRITICAL',
                'icon'       => 'arrow-down-tray',
                'color'      => 'orange',
                'messages'   => [
                    'investigating' => 'Chers partenaires marchands, la réception de paiements clients via Orange Money subit des perturbations. Nos techniciens sont mobilisés pour rétablir vos encaissements au plus vite.',
                    'identified'    => 'Le dysfonctionnement lié aux encaissements Orange Money est corrigé au niveau de la passerelle. La remise en route est en cours.',
                    'monitoring'    => 'Les encaissements Orange Money passent désormais normalement. Nous veillons à la notification instantanée de chaque paiement.',
                    'resolved'      => 'Vos encaissements Orange Money sont totalement opérationnels et sécurisés.'
                ]
            ],
            [
                'key'        => 'mtn_disbursement',
                'category'   => 'momo',
                'category_label' => 'Mobile Money & Télécoms',
                'name'       => 'MTN MoMo : Disbursement (Retraits)',
                'alert_title'=> 'Retard Transferts Sortants MTN',
                'severity'   => 'CRITICAL',
                'icon'       => 'arrow-up-tray',
                'color'      => 'amber',
                'messages'   => [
                    'investigating' => 'Chers partenaires, les transferts de fonds et retraits vers les comptes MTN MoMo accusent un retard de confirmation. Vos fonds restent en sécurité et nous traitons la file d\'envoi.',
                    'identified'    => 'Le ralentissement sur les transferts sortants MTN a été résolu sur nos serveurs. L\'évacuation des transferts en attente débute.',
                    'monitoring'    => 'Les retraits et virements MTN MoMo aboutissent à nouveau rapidement. Nous surveillons les accusés de réception.',
                    'resolved'      => 'Les transferts sortants MTN Mobile Money fonctionnent avec une rapidité nominale.'
                ]
            ],
            [
                'key'        => 'orange_disbursement',
                'category'   => 'momo',
                'category_label' => 'Mobile Money & Télécoms',
                'name'       => 'Orange Money : Disbursement',
                'alert_title'=> 'Retard Transferts Sortants Orange',
                'severity'   => 'CRITICAL',
                'icon'       => 'arrow-up-tray',
                'color'      => 'orange',
                'messages'   => [
                    'investigating' => 'Chers partenaires, des délais sont constatés lors de l\'exécution des retraits et virements Orange Money. Nous travaillons à débloquer les transmissions au plus vite.',
                    'identified'    => 'La cause des retards sur les paiements sortants Orange Money est résolue. La reprise des flux est en cours.',
                    'monitoring'    => 'Les transferts vers Orange Money sont de nouveau confirmés sans attente. Surveillance continue maintenue.',
                    'resolved'      => 'Le service de retrait et transfert Orange Money est entièrement rétabli.'
                ]
            ],
            [
                'key'        => 'mtn_airtime',
                'category'   => 'momo',
                'category_label' => 'Mobile Money & Télécoms',
                'name'       => 'MTN Recharge / Airtime',
                'alert_title'=> 'Délai Recharge Crédit MTN',
                'severity'   => 'CRITICAL',
                'icon'       => 'signal',
                'color'      => 'yellow',
                'messages'   => [
                    'investigating' => 'Chers agents, les ventes de crédit de communication MTN subissent un délai d\'émission. Nos équipes analysent la liaison pour vous permettre de servir vos clients rapidement.',
                    'identified'    => 'Le canal de distribution de crédit MTN a été débloqué. L\'envoi automatique des recharges redémarre.',
                    'monitoring'    => 'Le crédit téléphonique MTN est de nouveau distribué instantanément. Nous vérifions les réceptions sur les combinés.',
                    'resolved'      => 'La recharge de crédit MTN fonctionne à 100 %. Merci de votre confiance.'
                ]
            ],
            [
                'key'        => 'orange_airtime',
                'category'   => 'momo',
                'category_label' => 'Mobile Money & Télécoms',
                'name'       => 'Orange Recharge / Airtime',
                'alert_title'=> 'Délai Recharge Crédit Orange',
                'severity'   => 'CRITICAL',
                'icon'       => 'signal',
                'color'      => 'orange',
                'messages'   => [
                    'investigating' => 'Chers agents et clients, la livraison du crédit d\'appel Orange rencontre une lenteur temporaire. Nous faisons le nécessaire pour rétablir ce service au plus tôt.',
                    'identified'    => 'La liaison d\'envoi de crédit Orange est rétablie. La synchronisation est en phase finale.',
                    'monitoring'    => 'Les recharges de communication Orange s\'exécutent de nouveau sans attente. Nous gardons le système sous contrôle.',
                    'resolved'      => 'Le service de recharge de crédit Orange est parfaitement opérationnel.'
                ]
            ],
            [
                'key'        => 'camtel',
                'category'   => 'momo',
                'category_label' => 'Mobile Money & Télécoms',
                'name'       => 'Camtel Recharge / Top-up',
                'alert_title'=> 'Interruption Recharges Camtel Blue',
                'severity'   => 'CRITICAL',
                'icon'       => 'wifi',
                'color'      => 'blue',
                'messages'   => [
                    'investigating' => 'Chers partenaires, la vente d\'unités et de forfaits internet Camtel Blue connaît des interruptions momentanées. Nos techniciens rétablissent la connexion avec le réseau Camtel.',
                    'identified'    => 'L\'origine de l\'interruption avec le réseau Camtel est corrigée. La réactivation du service est en cours.',
                    'monitoring'    => 'Les forfaits et recharges Camtel s\'activent avec succès. Nous confirmons la livraison de chaque demande.',
                    'resolved'      => 'Le service de recharge Camtel Blue fonctionne à nouveau de façon fluide et continue.'
                ]
            ],

            // --- 3. FACTURES D'ÉNERGIE & D'EAU ---
            [
                'key'        => 'eneo',
                'category'   => 'utilities',
                'category_label' => 'Énergie & Eau',
                'name'       => 'Factures ENEO (Électricité / Prépayé)',
                'alert_title'=> 'Perturbation Paiement Factures ENEO',
                'severity'   => 'CRITICAL',
                'icon'       => 'bolt',
                'color'      => 'amber',
                'messages'   => [
                    'investigating' => 'Chers partenaires et usagers, le règlement des factures d\'électricité et la génération des jetons prépayés ENEO sont temporairement différés. Nous intervenons immédiatement pour relancer le service.',
                    'identified'    => 'La communication avec le serveur de facturation ENEO a été rétablie. Le moteur d\'émission des codes prépayés et quittances redémarre.',
                    'monitoring'    => 'Les paiements de factures et l\'achat de jetons ENEO s\'effectuent normalement. Nous vérifions la bonne remise des reçus.',
                    'resolved'      => 'Le service de paiement et d\'achat de crédit d\'électricité ENEO est entièrement opérationnel.'
                ]
            ],
            [
                'key'        => 'camwater',
                'category'   => 'utilities',
                'category_label' => 'Énergie & Eau',
                'name'       => 'Factures Camwater (Eau)',
                'alert_title'=> 'Lenteurs Factures Camwater',
                'severity'   => 'CRITICAL',
                'icon'       => 'sparkles',
                'color'      => 'cyan',
                'messages'   => [
                    'investigating' => 'Chers usagers, la validation des règlements de factures d\'eau Camwater présente des lenteurs. Nos équipes vérifient le lien avec le distributeur d\'eau.',
                    'identified'    => 'Le point de blocage sur le paiement des quittances Camwater a été levé. La validation des règlements reprend.',
                    'monitoring'    => 'Les quittances Camwater sont de nouveau émises sans retard. Nous suivons attentivement les opérations en direct.',
                    'resolved'      => 'Le service de règlement des factures Camwater est totalement rétabli.'
                ]
            ],

            // --- 4. RÉABONNEMENTS TÉLÉVISION ---
            [
                'key'        => 'canal',
                'category'   => 'tv',
                'category_label' => 'Télévision & Médias',
                'name'       => 'Canal+ Télévision',
                'alert_title'=> 'Retard Réabonnements Canal+',
                'severity'   => 'CRITICAL',
                'icon'       => 'tv',
                'color'      => 'slate',
                'messages'   => [
                    'investigating' => 'Chers abonnés et agents, la confirmation des réabonnements Canal+ et le réaffichage des images connaissent un délai d\'attente. Nous traitons la passerelle d\'activation sans attendre.',
                    'identified'    => 'La synchronisation avec le distributeur Canal+ a été corrigée. Les signaux de réactivation sont en cours de transmission.',
                    'monitoring'    => 'Les images TV et les formules Canal+ sont de nouveau réactivées immédiatement. Nous surveillons le retour des chaînes.',
                    'resolved'      => 'Le service de réabonnement Canal+ est 100 % fonctionnel. Bon divertissement à tous !'
                ]
            ],
            [
                'key'        => 'dstv',
                'category'   => 'tv',
                'category_label' => 'Télévision & Médias',
                'name'       => 'DSTV Télévision',
                'alert_title'=> 'Délai Activation Bouquets DSTV',
                'severity'   => 'CRITICAL',
                'icon'       => 'tv',
                'color'      => 'blue',
                'messages'   => [
                    'investigating' => 'Chers abonnés, le renouvellement des bouquets DSTV subit un ralentissement temporaire. Nos équipes techniques effectuent les réglages indispensables.',
                    'identified'    => 'La passerelle de réactivation DSTV est débloquée. Les renouvellements sont transmis sans encombre.',
                    'monitoring'    => 'Les bouquets DSTV sont réactivés dans les délais habituels. Nous nous assurons du rétablissement complet des chaînes.',
                    'resolved'      => 'Le service DSTV fonctionne à nouveau impeccablement.'
                ]
            ],
            [
                'key'        => 'startimes',
                'category'   => 'tv',
                'category_label' => 'Télévision & Médias',
                'name'       => 'StarTimes TV',
                'alert_title'=> 'Perturbation Réabonnements StarTimes',
                'severity'   => 'CRITICAL',
                'icon'       => 'tv',
                'color'      => 'rose',
                'messages'   => [
                    'investigating' => 'Chers partenaires, les réabonnements aux décodeurs StarTimes rencontrent des échecs passagers. Nos équipes sont mobilisées pour réinitialiser les liaisons.',
                    'identified'    => 'La source de l\'anomalie sur le réseau StarTimes a été réparée. Le système valide de nouveau les cartes.',
                    'monitoring'    => 'Les réactivations de cartes StarTimes se déroulent normalement. Nous confirmons l\'accès aux bouquets.',
                    'resolved'      => 'Le service de réabonnement StarTimes est complètement rétabli.'
                ]
            ],

            // --- 5. SERVICES RÉGIONAUX & DISTRIBUTION ---
            [
                'key'        => 'mtn_congo',
                'category'   => 'regional',
                'category_label' => 'Régional (Congo)',
                'name'       => 'MTN Mobile Money Congo',
                'alert_title'=> 'Lenteurs MTN Mobile Money Congo',
                'severity'   => 'CRITICAL',
                'icon'       => 'globe-alt',
                'color'      => 'emerald',
                'messages'   => [
                    'investigating' => 'Chers partenaires régionaux, des retards sont constatés sur les transactions transfrontalières MTN Mobile Money Congo. Nous analysons le couloir de transmission en priorité.',
                    'identified'    => 'La liaison interbancaire régionale a été stabilisée. Les transferts vers le Congo reprennent progressivement.',
                    'monitoring'    => 'Les opérations MTN MoMo Congo sont traitées sans délai anormal. Nous suivons l\'acheminement des flux.',
                    'resolved'      => 'Le service MTN Mobile Money Congo est entièrement opérationnel.'
                ]
            ],
            [
                'key'        => 'sabc',
                'category'   => 'sabc',
                'category_label' => 'Boissons du Cameroun',
                'name'       => 'Boissons du Cameroun (SABC)',
                'alert_title'=> 'Délai Validation Commandes SABC',
                'severity'   => 'CRITICAL',
                'icon'       => 'truck',
                'color'      => 'red',
                'messages'   => [
                    'investigating' => 'Chers distributeurs et partenaires, le règlement des commandes de boissons SABC rencontre des délais de validation. Soyez assurés que vos commandes sont enregistrées et prises en compte par nos équipes.',
                    'identified'    => 'L\'origine du délai sur les ordres de paiement SABC est résolue. La validation des bons d\'approvisionnement est relancée.',
                    'monitoring'    => 'Les paiements de commandes SABC sont de nouveau validés instantanément. Nous contrôlons les confirmations d\'encaissement.',
                    'resolved'      => 'Le service de paiement des commandes marchands SABC est pleinement opérationnel. Bonnes ventes à vous !'
                ]
            ],
        ];
    }

    /**
     * Recherche un scénario par sa clé
     */
    public static function findScenario(string $key): ?array
    {
        foreach (self::getScenarios() as $scenario) {
            if ($scenario['key'] === $key) {
                return $scenario;
            }
        }
        return null;
    }

    /**
     * Exécute un scénario : transmission n8n + insertion locale VigilCore
     */
    public static function trigger(string $key, bool $sendToN8n = true, ?string $customSeverity = null): ?Incident
    {
        $scenario = self::findScenario($key);
        if (!$scenario) {
            return null;
        }

        $hostName = 'srv901529';
        $n8nUrl   = config('services.n8n.webhook_url', 'https://n8n.srv901529.hstgr.cloud/webhook/vigilcore-alert');
        $severity = strtoupper($customSeverity ?? $scenario['severity'] ?? 'CRITICAL');
        $nowDouala = now()->timezone('Africa/Douala');

        $payload = [
            'alert_name'            => $scenario['alert_title'],
            'component'             => $scenario['key'],
            'service_name'          => $scenario['name'],
            'title'                 => $scenario['alert_title'],
            'severity'              => $severity,
            'status'                => 'firing',
            'root_cause'            => 'Délai d\'attente dépassé (Timeout 504) ou anomalie de passerelle réseau détectée par les sondes Kibana / Zabbix.',
            'error_code'            => 'ERR_GATEWAY_TIMEOUT_504',
            'http_status'           => 504,
            'business_impact'       => 'Ralentissement ou échec temporaire des validations de transactions pour les usagers et marchands.',
            'recommended_action'    => 'Vérifier la connectivité de la passerelle partenaire et relancer le microservice de synchronisation.',
            'affected_endpoints'    => ['/api/v2/' . $scenario['key'] . '/validate', '/api/v2/' . $scenario['key'] . '/status'],
            'message'               => $scenario['messages']['investigating'],
            'description'           => $scenario['messages']['investigating'],
            'message_investigating' => $scenario['messages']['investigating'],
            'message_identified'    => $scenario['messages']['identified'],
            'message_monitoring'    => $scenario['messages']['monitoring'],
            'message_resolved'      => $scenario['messages']['resolved'],
            'server'                => $hostName,
            'environment'           => 'Production (' . $hostName . ')',
            'datacenter'            => 'Douala Datacenter (Cameroun) • Cloudflare Edge',
            'timezone'              => 'Africa/Douala (UTC+1 / WAT - Cameroun)',
            'triggered_at_wat'      => $nowDouala->format('d/m/Y H:i:s') . ' (WAT - Douala)',
            'simulated_at'          => $nowDouala->toIso8601String(),
        ];

        // 1. Envoi HTTP au Webhook n8n
        $n8nSent = false;
        if ($sendToN8n && !empty($n8nUrl)) {
            try {
                $response = Http::timeout(3)->post($n8nUrl, $payload);
                $n8nSent = $response->successful();
            } catch (\Exception $e) {
                Log::warning("Simulation n8n dispatch failed ({$key}): " . $e->getMessage());
            }
        }

        // 2. Création de l'incident dans VigilCore
        $incident = Incident::create([
            'title'        => $scenario['alert_title'],
            'description'  => $scenario['messages']['investigating'],
            'severity'     => $severity,
            'status'       => 'open',
            'source'       => 'Kibana Logs Engine',
            'component'    => $scenario['key'],
            'server'       => $hostName,
            'raw_payload'  => array_merge($payload, [
                'n8n_dispatched' => $n8nSent,
                'scenario_key'   => $key,
            ]),
        ]);

        \Illuminate\Support\Facades\Cache::forget('vigilcore_active_counts');
        \Illuminate\Support\Facades\Cache::forget('vigilcore_dashboard_counts');
        \Illuminate\Support\Facades\Cache::forget('vigilcore_reports_kpis_today');
        \Illuminate\Support\Facades\Cache::forget('vigilcore_reports_kpis_week');
        \Illuminate\Support\Facades\Cache::forget('vigilcore_reports_kpis_month');
        \Illuminate\Support\Facades\Cache::forget('vigilcore_reports_kpis_all');

        return $incident;
    }

}
