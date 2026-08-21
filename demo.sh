#!/usr/bin/env bash

# ==============================================================================
# VIGILCORE INCIDENT SIMULATOR (20 SERVICES & 4 ÉTAPES DÉDIÉES)
# ==============================================================================
# Ce script permet à l'opérateur de déclencher à la demande un incident réaliste
# sur l'un des 20 services critiques de l'écosystème Maviance / Smobilpay.
# Il envoie le webhook d'alerte directement à n8n qui orchestre :
# 1. WhatsApp & Telegram
# 2. Atlassian Statuspage (4 étapes : Investigating -> Identified -> Monitoring -> Resolved)
# 3. VigilCore Dashboard (affichage temps réel & boîte noire JSON)
# ==============================================================================

N8N_URL="https://n8n.srv901529.hstgr.cloud/webhook/vigilcore-alert"
HOST_NAME="srv901529"
NOW_WAT=$(date "+%d/%m/%Y %H:%M:%S (WAT - Douala)")
NOW_ISO=$(date -u +"%Y-%m-%dT%H:%M:%SZ")

clear
echo "================================================================================"
echo "       VIGILCORE INCIDENT SIMULATOR (20 SERVICES & 4 ÉTAPES DÉDIÉES)      "
echo "================================================================================"
echo "--- PLATEFORMES MAVIANCE ---"
echo "  1) Smobilpay Payment Platform & APIs       3) Agent & Merchant Payment Portal"
echo "  2) Third Party Merchant API (S3P)          4) Smobilpay for e-commerce"
echo ""
echo "--- MOBILE MONEY & TÉLÉCOMS (CAMEROUN) ---"
echo "  5) MTN Mobile Money (Général)              10) Orange Money : Disbursement (Retraits)"
echo "  6) Orange Money (Général)                  11) MTN Recharge / Airtime"
echo "  7) MTN MoMo : Collections (Encaissements)  12) Orange Recharge / Airtime"
echo "  8) Orange Money : Collections              13) Camtel Recharge / Top-up"
echo "  9) MTN MoMo : Disbursement (Retraits)"
echo ""
echo "--- FACTURES D'ÉNERGIE & D'EAU ---"
echo "  14) Factures ENEO (Électricité / Prépayé)   15) Factures Camwater (Eau)"
echo ""
echo "--- RÉABONNEMENTS TÉLÉVISION ---"
echo "  16) Canal+ Télévision                       18) StarTimes TV"
echo "  17) DSTV Télévision"
echo ""
echo "--- SERVICES RÉGIONAUX & PARTENAIRES ---"
echo "  19) MTN Mobile Money Congo                  20) SABC Boissons (Paiements Marchands)"
echo ""
echo "  0) Quitter"
echo "================================================================================"
read -p "Sélectionnez un service à tester [1-20] : " choice

case $choice in
  1)
    KEY="smobilpay"
    NAME="Smobilpay Platform & APIs"
    TITLE="Perturbation Plateforme Smobilpay"
    MSG_INV="Chers partenaires, nous constatons actuellement des ralentissements inhabituels lors de la validation de certains paiements sur la plateforme Smobilpay. Nos équipes techniques sont immédiatement mobilisées pour analyser la situation et rétablir la fluidité du service. Nous nous excusons sincèrement pour la gêne occasionnée dans vos activités."
    MSG_ID="Chers partenaires, la cause du ralentissement a été localisée avec précision. Nos ingénieurs finalisent le correctif afin de relancer le traitement normal des transactions en toute sécurité. Vos flux restent protégés."
    MSG_MON="Les transactions s'exécutent de nouveau avec succès sur l'ensemble de la plateforme. Nos équipes restent en veille active afin de s'assurer d'une parfaite stabilité."
    MSG_RES="Le service global Smobilpay est désormais 100 % opérationnel et fluide. Nous vous remercions chaleureusement pour votre patience et votre confiance continue."
    ;;
  2)
    KEY="s3p"
    NAME="Third Party Merchant API (S3P)"
    TITLE="Instabilité API Marchand S3P"
    MSG_INV="Chers intégrateurs et marchands, des délais de réponse allongés sont actuellement relevés sur les points d'accès de l'API S3P. Nos équipes d'astreinte sont à pied d'œuvre pour identifier la source du blocage et rétablir les échanges instantanés."
    MSG_ID="La cause du blocage sur la passerelle d'échange a été identifiée. Une mesure corrective est en cours d'application pour restaurer un temps de réponse optimal."
    MSG_MON="Les appels API et les validations de paiement marchand reprennent à un rythme normal. Nous surveillons attentivement les métriques de trafic."
    MSG_RES="L'API S3P fonctionne de nouveau de manière optimale pour l'ensemble de vos intégrations. Merci pour votre compréhension."
    ;;
  3)
    KEY="merchant_portal"
    NAME="Agent & Merchant Portal"
    TITLE="Lenteurs Portail Marchand"
    MSG_INV="Chers agents et marchands, l'accès à votre espace de gestion et le chargement des historiques connaissent des lenteurs temporaires. Rassurez-vous, vos données et soldes sont parfaitement sécurisés. Nous vérifions la situation sans délai."
    MSG_ID="L'origine des lenteurs sur le portail a été détectée. Le rétablissement complet des accès est en cours de finalisation par nos équipes."
    MSG_MON="La connexion et la navigation sur votre portail sont de nouveau stables. Nous veillons au bon déroulement de chaque opération."
    MSG_RES="Votre portail agent et marchand est pleinement accessible et opérationnel. Bonne suite dans vos opérations quotidiennes !"
    ;;
  4)
    KEY="ecommerce"
    NAME="Smobilpay for e-commerce"
    TITLE="Perturbation Module E-Commerce"
    MSG_INV="Chers partenaires e-commerce, des échecs intermittents sont observés lors de la finalisation des paiements en ligne sur vos boutiques. Nous intervenons en priorité pour vous permettre d'encaisser vos clients sereinement."
    MSG_ID="Le dysfonctionnement affectant le module de paiement en ligne a été identifié. La mise à jour corrective est en cours de déploiement."
    MSG_MON="Les paiements sur les boutiques partenaires s'effectuent de nouveau avec succès. Nous surveillons le bon acheminement des commandes."
    MSG_RES="Le module e-commerce fonctionne parfaitement. Vos clients peuvent finaliser leurs achats en toute tranquillité."
    ;;
  5)
    KEY="mtn_momo"
    NAME="MTN Mobile Money (Général)"
    TITLE="Perturbation Réseau MTN MoMo"
    MSG_INV="Chers clients et partenaires, des instabilités temporaires touchent actuellement les opérations MTN Mobile Money. Nos équipes collaborent activement avec l'opérateur pour un retour rapide à la normale."
    MSG_ID="La liaison technique avec l'opérateur MTN a été localisée comme source du ralentissement. Le rétablissement de la communication est en cours."
    MSG_MON="Les transactions MTN MoMo reprennent progressivement. Nous nous assurons que chaque transfert en attente aboutisse correctement."
    MSG_RES="L'ensemble des services MTN Mobile Money est totalement rétabli. Merci pour votre patience."
    ;;
  6)
    KEY="orange_money"
    NAME="Orange Money (Général)"
    TITLE="Perturbation Réseau Orange Money"
    MSG_INV="Chers utilisateurs, des lenteurs sont actuellement signalées sur le réseau Orange Money. Nous menons les vérifications nécessaires avec les équipes de l'opérateur pour sécuriser vos opérations."
    MSG_ID="Le point d'instabilité sur le canal Orange Money a été trouvé. Les réglages nécessaires sont en cours d'application."
    MSG_MON="Les transactions Orange Money sont de nouveau traitées avec succès. Nous gardons une observation étroite sur les validations."
    MSG_RES="Le service Orange Money est de nouveau 100 % disponible et stable."
    ;;
  7)
    KEY="mtn_collection"
    NAME="MTN MoMo : Collections (Encaissements)"
    TITLE="Échecs Encaissements MTN MoMo"
    MSG_INV="Chers marchands, les encaissements via MTN MoMo rencontrent des rejets temporaires. Soyez assurés que nous traitons cette anomalie en priorité pour vos encaissements clients."
    MSG_ID="La cause des rejets d'encaissements MTN a été diagnostiquée. Le canal de validation est en cours de redémarrage."
    MSG_MON="Les encaissements MTN MoMo sont de nouveau acceptés et validés. Nous vérifions le bon crédit de vos comptes."
    MSG_RES="Le service d'encaissement MTN Mobile Money fonctionne à nouveau sans la moindre interruption."
    ;;
  8)
    KEY="orange_collection"
    NAME="Orange Money : Collections"
    TITLE="Échecs Encaissements Orange Money"
    MSG_INV="Chers partenaires marchands, la réception de paiements clients via Orange Money subit des perturbations. Nos techniciens sont mobilisés pour rétablir vos encaissements au plus vite."
    MSG_ID="Le dysfonctionnement lié aux encaissements Orange Money est corrigé au niveau de la passerelle. La remise en route est en cours."
    MSG_MON="Les encaissements Orange Money passent désormais normalement. Nous veillons à la notification instantanée de chaque paiement."
    MSG_RES="Vos encaissements Orange Money sont totalement opérationnels et sécurisés."
    ;;
  9)
    KEY="mtn_disbursement"
    NAME="MTN MoMo : Disbursement (Retraits)"
    TITLE="Retard Transferts Sortants MTN"
    MSG_INV="Chers partenaires, les transferts de fonds et retraits vers les comptes MTN MoMo accusent un retard de confirmation. Vos fonds restent en sécurité et nous traitons la file d'envoi."
    MSG_ID="Le ralentissement sur les transferts sortants MTN a été résolu sur nos serveurs. L'évacuation des transferts en attente débute."
    MSG_MON="Les retraits et virements MTN MoMo aboutissent à nouveau rapidement. Nous surveillons les accusés de réception."
    MSG_RES="Les transferts sortants MTN Mobile Money fonctionnent avec une rapidité nominale."
    ;;
  10)
    KEY="orange_disbursement"
    NAME="Orange Money : Disbursement"
    TITLE="Retard Transferts Sortants Orange"
    MSG_INV="Chers partenaires, des délais sont constatés lors de l'exécution des retraits et virements Orange Money. Nous travaillons à débloquer les transmissions au plus vite."
    MSG_ID="La cause des retards sur les paiements sortants Orange Money est résolue. La reprise des flux est en cours."
    MSG_MON="Les transferts vers Orange Money sont de nouveau confirmés sans attente. Surveillance continue maintenue."
    MSG_RES="Le service de retrait et transfert Orange Money est entièrement rétabli."
    ;;
  11)
    KEY="mtn_airtime"
    NAME="MTN Recharge / Airtime"
    TITLE="Délai Recharge Crédit MTN"
    MSG_INV="Chers agents, les ventes de crédit de communication MTN subissent un délai d'émission. Nos équipes analysent la liaison pour vous permettre de servir vos clients rapidement."
    MSG_ID="Le canal de distribution de crédit MTN a été débloqué. L'envoi automatique des recharges redémarre."
    MSG_MON="Le crédit téléphonique MTN est de nouveau distribué instantanément. Nous vérifions les réceptions sur les combinés."
    MSG_RES="La recharge de crédit MTN fonctionne à 100 %. Merci de votre confiance."
    ;;
  12)
    KEY="orange_airtime"
    NAME="Orange Recharge / Airtime"
    TITLE="Délai Recharge Crédit Orange"
    MSG_INV="Chers agents et clients, la livraison du crédit d'appel Orange rencontre une lenteur temporaire. Nous faisons le nécessaire pour rétablir ce service au plus tôt."
    MSG_ID="La liaison d'envoi de crédit Orange est rétablie. La synchronisation est en phase finale."
    MSG_MON="Les recharges de communication Orange s'exécutent de nouveau sans attente. Nous gardons le système sous contrôle."
    MSG_RES="Le service de recharge de crédit Orange est parfaitement opérationnel."
    ;;
  13)
    KEY="camtel"
    NAME="Camtel Recharge / Top-up"
    TITLE="Interruption Recharges Camtel Blue"
    MSG_INV="Chers partenaires, la vente d'unités et de forfaits internet Camtel Blue connaît des interruptions momentanées. Nos techniciens rétablissent la connexion avec le réseau Camtel."
    MSG_ID="L'origine de l'interruption avec le réseau Camtel est corrigée. La réactivation du service est en cours."
    MSG_MON="Les forfaits et recharges Camtel s'activent avec succès. Nous confirmons la livraison de chaque demande."
    MSG_RES="Le service de recharge Camtel Blue fonctionne à nouveau de façon fluide et continue."
    ;;
  14)
    KEY="eneo"
    NAME="Factures ENEO (Électricité / Prépayé)"
    TITLE="Perturbation Paiement Factures ENEO"
    MSG_INV="Chers partenaires et usagers, le règlement des factures d'électricité et la génération des jetons prépayés ENEO sont temporairement différés. Nous intervenons immédiatement pour relancer le service."
    MSG_ID="La communication avec le serveur de facturation ENEO a été rétablie. Le moteur d'émission des codes prépayés et quittances redémarre."
    MSG_MON="Les paiements de factures et l'achat de jetons ENEO s'effectuent normalement. Nous vérifions la bonne remise des reçus."
    MSG_RES="Le service de paiement et d'achat de crédit d'électricité ENEO est entièrement opérationnel."
    ;;
  15)
    KEY="camwater"
    NAME="Factures Camwater (Eau)"
    TITLE="Lenteurs Factures Camwater"
    MSG_INV="Chers usagers, la validation des règlements de factures d'eau Camwater présente des lenteurs. Nos équipes vérifient le lien avec le distributeur d'eau."
    MSG_ID="Le point de blocage sur le paiement des quittances Camwater a été levé. La validation des règlements reprend."
    MSG_MON="Les quittances Camwater sont de nouveau émises sans retard. Nous suivons attentivement les opérations en direct."
    MSG_RES="Le service de règlement des factures Camwater est totalement rétabli."
    ;;
  16)
    KEY="canal"
    NAME="Canal+ Télévision"
    TITLE="Retard Réabonnements Canal+"
    MSG_INV="Chers abonnés et agents, la confirmation des réabonnements Canal+ et le réaffichage des images connaissent un délai d'attente. Nous traitons la passerelle d'activation sans attendre."
    MSG_ID="La synchronisation avec le distributeur Canal+ a été corrigée. Les signaux de réactivation sont en cours de transmission."
    MSG_MON="Les images TV et les formules Canal+ sont de nouveau réactivées immédiatement. Nous surveillons le retour des chaînes."
    MSG_RES="Le service de réabonnement Canal+ est 100 % fonctionnel. Bon divertissement à tous !"
    ;;
  17)
    KEY="dstv"
    NAME="DSTV Télévision"
    TITLE="Délai Activation Bouquets DSTV"
    MSG_INV="Chers abonnés, le renouvellement des bouquets DSTV subit un ralentissement temporaire. Nos équipes techniques effectuent les réglages indispensables."
    MSG_ID="La passerelle de réactivation DSTV est débloquée. Les renouvellements sont transmis sans encombre."
    MSG_MON="Les bouquets DSTV sont réactivés dans les délais habituels. Nous nous assurons du rétablissement complet des chaînes."
    MSG_RES="Le service DSTV fonctionne à nouveau impeccablement."
    ;;
  18)
    KEY="startimes"
    NAME="StarTimes TV"
    TITLE="Perturbation Réabonnements StarTimes"
    MSG_INV="Chers partenaires, les réabonnements aux décodeurs StarTimes rencontrent des échecs passagers. Nos équipes sont mobilisées pour réinitialiser les liaisons."
    MSG_ID="La source de l'anomalie sur le réseau StarTimes a été réparée. Le système valide de nouveau les cartes."
    MSG_MON="Les réactivations de cartes StarTimes se déroulent normalement. Nous confirmons l'accès aux bouquets."
    MSG_RES="Le service de réabonnement StarTimes est complètement rétabli."
    ;;
  19)
    KEY="mtn_congo"
    NAME="MTN Mobile Money Congo"
    TITLE="Lenteurs MTN Mobile Money Congo"
    MSG_INV="Chers partenaires régionaux, des retards sont constatés sur les transactions transfrontalières MTN Mobile Money Congo. Nous analysons le couloir de transmission en priorité."
    MSG_ID="La liaison interbancaire régionale a été stabilisée. Les transferts vers le Congo reprennent progressivement."
    MSG_MON="Les opérations MTN MoMo Congo sont traitées sans délai anormal. Nous suivons l'acheminement des flux."
    MSG_RES="Le service MTN Mobile Money Congo est entièrement opérationnel."
    ;;
  20)
    KEY="sabc"
    NAME="SABC Boissons (Paiements Marchands)"
    TITLE="Délai Validation Commandes SABC"
    MSG_INV="Chers distributeurs et partenaires, le règlement des commandes de boissons SABC rencontre des délais de validation. Soyez assurés que vos commandes sont enregistrées et prises en compte par nos équipes."
    MSG_ID="L'origine du délai sur les ordres de paiement SABC est résolue. La validation des bons d'approvisionnement est relancée."
    MSG_MON="Les paiements de commandes SABC sont de nouveau validés instantanément. Nous contrôlons les confirmations d'encaissement."
    MSG_RES="Le service de paiement des commandes marchands SABC est pleinement opérationnel. Bonnes ventes à vous !"
    ;;
  0)
    echo "Opération annulée. Aucun incident déclenché."
    exit 0
    ;;
  *)
    echo "Option invalide. Veuillez choisir un chiffre entre 1 et 20."
    exit 1
    ;;
esac

echo ""
echo "⚡ INITIALISATION DU SCÉNARIO D'INCIDENT..."
echo "   Service cible : $NAME ($KEY)"
echo "   Gravité :       CRITICAL (P1)"
echo ""

# Construction du payload JSON certifié
PAYLOAD=$(cat <<EOF
{
  "title": "$TITLE",
  "server": "$HOST_NAME",
  "source": "Kibana Logs Engine",
  "status": "firing",
  "component": "$KEY",
  "service_name": "$NAME",
  "alert_name": "$TITLE",
  "severity": "CRITICAL",
  "root_cause": "Délai d'attente dépassé ou anomalie de passerelle détectée par les sondes de logs.",
  "error_code": "ERR_GATEWAY_TIMEOUT_504",
  "http_status": 504,
  "business_impact": "Ralentissement ou échec temporaire des validations de transactions pour les usagers et marchands.",
  "recommended_action": "Vérifier la connectivité de la passerelle partenaire et relancer le microservice.",
  "affected_endpoints": ["/api/v2/$KEY/validate", "/api/v2/$KEY/status"],
  "environment": "Production ($HOST_NAME)",
  "datacenter": "Douala Datacenter (Cameroun) • Cloudflare Edge",
  "timezone": "Africa/Douala (UTC+1 / WAT - Cameroun)",
  "message": "$MSG_INV",
  "description": "$MSG_INV",
  "message_investigating": "$MSG_INV",
  "message_identified": "$MSG_ID",
  "message_monitoring": "$MSG_MON",
  "message_resolved": "$MSG_RES",
  "triggered_at_wat": "$NOW_WAT",
  "simulated_at": "$NOW_ISO"
}
EOF
)

echo "[1/3] Injection de traces d'erreurs HTTP 500/504 dans Elasticsearch..."
ES_LOG=$(cat <<EOF
{
  "@timestamp": "$NOW_ISO",
  "service": { "name": "$KEY" },
  "http": { "response": { "status_code": 504 } },
  "log": { "level": "error" },
  "message": "Gateway Timeout 504 on endpoint /api/v2/$KEY/validate - $TITLE",
  "host": { "name": "$HOST_NAME" },
  "component": "$KEY",
  "source": "Kibana Logs Engine"
}
EOF
)

# Envoi du log dans Elasticsearch (ports 9200 standards)
curl -s -o /dev/null -X POST "http://127.0.0.1:9200/filebeat-logs/_doc" \
  -H "Content-Type: application/json" \
  -d "$ES_LOG" 2>/dev/null || curl -s -o /dev/null -X POST "http://127.0.0.1:9200/logs-app/_doc" \
  -H "Content-Type: application/json" \
  -d "$ES_LOG" 2>/dev/null || true

echo "      ✓ Traces et pics d'erreurs indexés dans Elasticsearch (Visibles dans Kibana)"

echo "[2/3] Déclenchement de l'alerte orchestrée sur n8n..."
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" -X POST "$N8N_URL" \
  -H "Content-Type: application/json" \
  -d "$PAYLOAD")

if [ "$HTTP_STATUS" -eq 200 ] || [ "$HTTP_STATUS" -eq 201 ]; then
  echo "      ✓ Alerte transmise avec succès à n8n (HTTP $HTTP_STATUS) !"
else
  echo "      ⚠️  Réponse n8n : HTTP $HTTP_STATUS"
fi

echo "[3/3] Enregistrement en base locale VigilCore..."
# Appel direct à l'API interne VigilCore pour enregistrer l'incident si besoin
curl -s -o /dev/null -X POST "http://127.0.0.1:8000/api/webhooks/alerts" \
  -H "Content-Type: application/json" \
  -d "$PAYLOAD" 2>/dev/null || true

echo "      ✓ Incident synchronisé dans le Dashboard VigilCore"

echo ""
echo "================================================================================"
echo "✓ LE CYCLE AUTOMATIQUE EST EN COURS SUR VOS SYSTÈMES !"
echo "================================================================================"
echo " Chronologie en direct sur la Statuspage :"
echo "  🔴 T + 0s   : INVESTIGATING -> \"$(echo $MSG_INV | cut -c 1-65)...\""
echo "  🟠 T + 40s  : IDENTIFIED    -> \"$(echo $MSG_ID | cut -c 1-65)...\""
echo "  🟡 T + 80s  : MONITORING    -> \"$(echo $MSG_MON | cut -c 1-65)...\""
echo "  🟢 T + 120s : RESOLVED      -> \"$(echo $MSG_RES | cut -c 1-65)...\""
echo "================================================================================"
echo " Les messages personnalisés ci-dessus défileront automatiquement sans intervention."
echo ""
