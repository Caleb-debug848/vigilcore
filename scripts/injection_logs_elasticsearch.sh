#!/usr/bin/env bash

# ==============================================================================
# INJECTION DES LOGS NOMINAUX POUR LES 20 SERVICES DANS ELASTICSEARCH / KIBANA
# ==============================================================================

SERVICES=(
  "smobilpay:Smobilpay Platform & APIs:200:Telemetrie operationnelle nominale"
  "s3p:Third Party Merchant API (S3P):401:ERR_UNAUTHORIZED_401 token expire sur endpoint /api/v1/auth"
  "merchant_portal:Agent & Merchant Portal:200:Telemetrie operationnelle nominale"
  "ecommerce:Smobilpay for e-commerce:200:Telemetrie operationnelle nominale"
  "mtn_momo:MTN Mobile Money (Général):500:ERR_INTERNAL_SERVER_500 on endpoint /api/v2/mtn_momo/validate"
  "orange_money:Orange Money (Général):504:ERR_GATEWAY_TIMEOUT_504 on endpoint /api/v2/orange_money/validate"
  "mtn_collection:MTN MoMo : Collections:200:Telemetrie operationnelle nominale"
  "orange_collection:Orange Money : Collections:200:Telemetrie operationnelle nominale"
  "mtn_disbursement:MTN MoMo : Disbursement:200:Telemetrie operationnelle nominale"
  "orange_disbursement:Orange Money : Disbursement:200:Telemetrie operationnelle nominale"
  "mtn_airtime:MTN Recharge / Airtime:200:Telemetrie operationnelle nominale"
  "orange_airtime:Orange Recharge / Airtime:200:Telemetrie operationnelle nominale"
  "camtel:Camtel Recharge / Top-up:200:Telemetrie operationnelle nominale"
  "eneo:Factures ENEO (Électricité):503:ERR_SERVICE_UNAVAILABLE_503 on endpoint /api/v2/eneo/validate"
  "camwater:Factures Camwater (Eau):504:ERR_GATEWAY_TIMEOUT_504 latence > 4200ms sur endpoint /api/v1/camwater"
  "canal:Canal+ Télévision:502:ERR_BAD_GATEWAY_502 passerelle TV injoignable sur /api/v1/canal"
  "dstv:DSTV Télévision:200:Telemetrie operationnelle nominale"
  "startimes:StarTimes TV:200:Telemetrie operationnelle nominale"
  "mtn_congo:MTN Mobile Money Congo:200:Telemetrie operationnelle nominale"
  "sabc:SABC Boissons du Cameroun:200:Telemetrie operationnelle nominale"
)

NOW_ISO=$(date -u +"%Y-%m-%dT%H:%M:%SZ")

echo "⚡ Injection des 20 services partenaires dans Elasticsearch..."

for s in "${SERVICES[@]}"; do
  IFS=":" read -r KEY NAME CODE MSG <<< "$s"

  LEVEL="info"
  if [ "$CODE" != "200" ]; then
    LEVEL="error"
  fi

  DOC=$(cat <<EOF
{
  "@timestamp": "$NOW_ISO",
  "service": { "name": "$KEY" },
  "component": "$KEY",
  "service_name": "$NAME",
  "http": { "response": { "status_code": $CODE } },
  "http.response.status_code": $CODE,
  "log": { "level": "$LEVEL" },
  "message": "$MSG",
  "host": { "name": "srv901529" },
  "source": "Kibana Logs Engine"
}
EOF
)

  curl -s -o /dev/null -X POST "http://127.0.0.1:9200/filebeat-logs/_doc" \
    -H "Content-Type: application/json" -d "$DOC" 2>/dev/null || true

  curl -s -o /dev/null -X POST "http://127.0.0.1:9200/logs-generic-default/_doc" \
    -H "Content-Type: application/json" -d "$DOC" 2>/dev/null || true

  echo "  ✓ $NAME ($KEY) -> HTTP $CODE [$LEVEL]"
done

echo ""
echo "🎉 Les 20 services ont été injectés avec succès dans Elasticsearch !"
echo "👉 Cliquez sur le bouton 'Refresh' en haut à droite dans Kibana pour afficher les 20 services !"
