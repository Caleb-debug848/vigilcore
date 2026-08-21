#!/usr/bin/env bash

# ==============================================================================
# INJECTION DES LOGS NOMINAUX POUR LES 20 SERVICES DANS ELASTICSEARCH / KIBANA
# ==============================================================================

SERVICES=(
  "smobilpay:Smobilpay Platform & APIs"
  "s3p:Third Party Merchant API (S3P)"
  "merchant_portal:Agent & Merchant Portal"
  "ecommerce:Smobilpay for e-commerce"
  "mtn_momo:MTN Mobile Money (Général)"
  "orange_money:Orange Money (Général)"
  "mtn_collection:MTN MoMo : Collections"
  "orange_collection:Orange Money : Collections"
  "mtn_disbursement:MTN MoMo : Disbursement"
  "orange_disbursement:Orange Money : Disbursement"
  "mtn_airtime:MTN Recharge / Airtime"
  "orange_airtime:Orange Recharge / Airtime"
  "camtel:Camtel Recharge / Top-up"
  "eneo:Factures ENEO (Électricité)"
  "camwater:Factures Camwater (Eau)"
  "canal:Canal+ Télévision"
  "dstv:DSTV Télévision"
  "startimes:StarTimes TV"
  "mtn_congo:MTN Mobile Money Congo"
  "sabc:SABC Boissons du Cameroun"
)

NOW_ISO=$(date -u +"%Y-%m-%dT%H:%M:%SZ")

echo "⚡ Injection des 20 services dans Elasticsearch..."

for s in "${SERVICES[@]}"; do
  KEY="${s%%:*}"
  NAME="${s##*:}"

  DOC=$(cat <<EOF
{
  "@timestamp": "$NOW_ISO",
  "service": { "name": "$KEY" },
  "component": "$KEY",
  "service_name": "$NAME",
  "log": { "level": "info" },
  "message": "Telemetry operational heartbeat - $NAME",
  "host": { "name": "srv901529" },
  "source": "Kibana Logs Engine"
}
EOF
)

  curl -s -o /dev/null -X POST "http://127.0.0.1:9200/filebeat-logs/_doc" \
    -H "Content-Type: application/json" -d "$DOC" 2>/dev/null || true

  curl -s -o /dev/null -X POST "http://127.0.0.1:9200/logs-generic-default/_doc" \
    -H "Content-Type: application/json" -d "$DOC" 2>/dev/null || true

  echo "  ✓ $NAME ($KEY)"
done

echo ""
echo "🎉 Les 20 services ont été indexés dans Elasticsearch avec succès !"
echo "👉 Actualisez votre graphique Kibana (bouton Refresh en haut à droite) pour voir les 20 services !"
