#!/usr/bin/env bash

# ==============================================================================
# VIGILCORE - ASSISTANT DE CONNEXION WHATSAPP (DIRECT & SANS CAMÉRA)
# ==============================================================================

clear
echo -e "\033[1;36m"
echo "  ╔═══════════════════════════════════════════════════════════════╗"
echo "  ║        VIGILCORE — CONNEXION RAPIDE WHATSAPP                 ║"
echo "  ╚═══════════════════════════════════════════════════════════════╝"
echo -e "\033[0m"

API_KEY=$(docker exec vigilcore-whatsapp printenv AUTHENTICATION_API_KEY 2>/dev/null)

if [ -z "$API_KEY" ]; then
    API_KEY="B6D711FCDE4D4FD5936544120E713976"
fi

# Vérification état actuel
STATE_RESP=$(curl -s -H "apikey: $API_KEY" http://localhost:8090/instance/connectionState/vigilcore-ops 2>/dev/null)
if echo "$STATE_RESP" | grep -q '"state":"open"'; then
    echo -e "\033[1;32m[✓] SUCCÈS : WhatsApp VigilCore est DÉJÀ CONNECTÉ et opérationnel !\033[0m\n"
    exit 0
fi

echo -e "Choisissez la méthode la plus rapide pour connecter votre téléphone :\n"
echo -e "  \033[1;32m[1]\033[0m \033[1;37mCode d'appairage à 8 lettres (RECOMMANDÉ — Sans Caméra)\033[0m"
echo -e "  \033[1;34m[2]\033[0m Afficher le QR Code directement dans ce terminal\033[0m"
echo -e "  \033[1;31m[3]\033[0m Réinitialiser complètement l'instance WhatsApp (Clean Reset)\033[0m\n"

read -p "Entrez votre choix (1, 2 ou 3) [Défaut: 1] : " user_choice
user_choice=${user_choice:-1}

if [ "$user_choice" = "3" ]; then
    echo -e "\n\033[1;33m[REINITIALISATION] Nettoyage de l'instance...\033[0m"
    docker restart vigilcore-whatsapp >/dev/null 2>&1
    sleep 4
    curl -s -X DELETE -H "apikey: $API_KEY" http://localhost:8090/instance/logout/vigilcore-ops >/dev/null 2>&1
    sleep 2
    echo -e "\033[1;32m[OK] Instance réinitialisée avec succès. Relancez ./connect_whatsapp.sh\033[0m\n"
    exit 0
fi

if [ "$user_choice" = "1" ]; then
    echo ""
    read -p "Entrez votre numéro WhatsApp avec indicatif pays (ex: 237690000000 ou 22960000000) : " phone_number
    # Nettoyage des espaces et du +
    phone_clean=$(echo "$phone_number" | tr -d ' ' | tr -d '+' | tr -d '-')
    
    if [ -z "$phone_clean" ]; then
        echo -e "\033[1;31mErreur : Numéro vide.\033[0m"
        exit 1
    fi

    echo -e "\n\033[1;33m[1/2] Demande du code d'appairage pour $phone_clean...\033[0m"
    PAIR_RESP=$(curl -s -H "apikey: $API_KEY" "http://localhost:8090/instance/connect/vigilcore-ops?number=${phone_clean}")
    
    PAIR_CODE=$(echo "$PAIR_RESP" | grep -o '"pairingCode":"[^"]*' | cut -d'"' -f4)
    
    if [ -n "$PAIR_CODE" ]; then
        echo -e "\n\033[1;32m═══════════════════════════════════════════════════════════════\033[0m"
        echo -e "  \033[1;37mVOTRE CODE D'APPAIRAGE WHATSAPP EST : \033[1;32m${PAIR_CODE}\033[0m"
        echo -e "\033[1;32m═══════════════════════════════════════════════════════════════\033[0m\n"
        echo -e "  \033[1;37mInstructions sur votre téléphone :\033[0m"
        echo -e "  1. Ouvrez \033[1mWhatsApp\033[0m."
        echo -e "  2. Allez dans \033[1mAppareils connectés\033[0m -> \033[1mConnecter un appareil\033[0m."
        echo -e "  3. En bas, appuyez sur : \033[1;36m« Associer avec un numéro de téléphone »\033[0m"
        echo -e "  4. Entrez ce code : \033[1;32m${PAIR_CODE}\033[0m\n"
        
        echo -e "\033[1;33mEn attente de votre validation sur WhatsApp (30s)...\033[0m"
        for i in {1..15}; do
            sleep 2
            CHECK=$(curl -s -H "apikey: $API_KEY" http://localhost:8090/instance/connectionState/vigilcore-ops 2>/dev/null)
            if echo "$CHECK" | grep -q '"state":"open"'; then
                echo -e "\n\033[1;32m[✓] FÉLICITATIONS : WhatsApp VigilCore est CONNECTÉ AVEC SUCCÈS !\033[0m"
                echo -e "\033[1;37mVous pouvez désormais lancer ./demo.sh et recevoir toutes vos alertes !\033[0m\n"
                exit 0
            fi
        done
        echo -e "\033[1;33mSi le délai est dépassé, relancez simplement ./connect_whatsapp.sh\033[0m\n"
    else
        echo -e "\033[1;31mRéponse du serveur :\033[0m $PAIR_RESP"
    fi
    exit 0
fi

if [ "$user_choice" = "2" ]; then
    echo -e "\n\033[1;33mGénération du QR Code dans le terminal...\033[0m"
    docker logs --tail 30 vigilcore-whatsapp
    echo -e "\n\033[1;37mScannez le QR Code affiché ci-dessus avec WhatsApp.\033[0m\n"
fi
