#!/usr/bin/env bash

# ==============================================================================
# VIGILCORE - REINITIALISATION TOTALE & GENERATION DU PNG OFFICIEL
# ==============================================================================

clear
echo -e "\033[1;36m[1/3] Réinitialisation et purge complète de l'instance WhatsApp...\033[0m"

API_KEY=$(docker exec vigilcore-whatsapp printenv AUTHENTICATION_API_KEY 2>/dev/null)
if [ -z "$API_KEY" ]; then
    API_KEY="B6D711FCDE4D4FD5936544120E713976"
fi

# Suppression de l'ancienne instance corrompue
curl -s -X DELETE -H "apikey: $API_KEY" http://localhost:8090/instance/delete/vigilcore-ops >/dev/null 2>&1
sleep 2

# Création d'une nouvelle instance toute neuve
echo -e "\033[1;36m[2/3] Création de la nouvelle instance propre 'vigilcore-ops'...\033[0m"
curl -s -X POST -H "apikey: $API_KEY" -H "Content-Type: application/json" \
  -d '{"instanceName":"vigilcore-ops","qrcode":true,"integration":"WHATSAPP-BAILEYS"}' \
  http://localhost:8090/instance/create >/dev/null 2>&1
sleep 2

# Extraction directe du QR Code en image PNG pure
echo -e "\033[1;36m[3/3] Génération de l'image PNG haute résolution...\033[0m"
RESP=$(curl -s -H "apikey: $API_KEY" http://localhost:8090/instance/connect/vigilcore-ops)
B64=$(echo "$RESP" | grep -o 'data:image/png;base64,[^"]*' | sed 's/data:image\/png;base64,//')

if [ -n "$B64" ]; then
    echo "$B64" | base64 -d > /var/www/vigilcore/public/qrcode.png
    chmod 644 /var/www/vigilcore/public/qrcode.png
    
    echo -e "\n\033[1;32m══════════════════════════════════════════════════════════════════\033[0m"
    echo -e "  \033[1;37m[✓] SUCCÈS : Votre QR Code officiel est généré !\033[0m"
    echo -e "  \033[1;37mOuvrez ce lien direct dans votre navigateur :\033[0m"
    echo -e "  \033[1;33m👉 https://vigilcore.calebdevs.com/qrcode.png\033[0m"
    echo -e "\033[1;32m══════════════════════════════════════════════════════════════════\033[0m\n"
    echo -e "  1. Ouvrez \033[1mhttps://vigilcore.calebdevs.com/qrcode.png\033[0m sur votre écran."
    echo -e "  2. Prenez votre téléphone : \033[1mWhatsApp -> Appareils connectés -> Connecter un appareil\033[0m."
    echo -e "  3. Scannez l'image affichée en plein écran.\n"
else
    echo -e "\033[1;31mErreur de génération. Réponse : $RESP\033[0m"
fi
