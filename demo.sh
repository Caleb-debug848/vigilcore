#!/usr/bin/env bash

# ==============================================================================
# VIGILCORE ENTERPRISE INCIDENT ORCHESTRATOR & SIMULATOR
# ==============================================================================
# Plateforme de Télémétrie, Observabilité & Alerting de Haute Disponibilité
# Écosystème : Maviance / Smobilpay • Douala Datacenter (Cameroun)
# Version : 2.4 Enterprise Live Edition — Présentation & Soutenance
# ==============================================================================

N8N_URL="https://n8n.srv901529.hstgr.cloud/webhook/vigilcore-alert"
N8N_EXECUTIONS_URL="https://n8n.srv901529.hstgr.cloud/workflow/7m9jgiRmCyyiel4P/executions"
HOST_NAME="srv901529"
DASHBOARD_URL="https://vigilcore.calebdevs.com/dashboard"
NOW_WAT=$(date "+%d/%m/%Y %H:%M:%S (WAT - Douala)")
NOW_ISO=$(date -u +"%Y-%m-%dT%H:%M:%SZ")

# --- COULEURS ANSI HAUTE DÉFINITION ---
C_RESET="\033[0m"
C_BOLD="\033[1m"
C_DIM="\033[2m"

# Palette Thématique VigilCore
C_BLUE="\033[1;38;5;33m"
C_NAVY="\033[38;5;27m"
C_CYAN="\033[1;38;5;51m"
C_GREEN="\033[1;38;5;48m"
C_AMBER="\033[1;38;5;214m"
C_PURPLE="\033[1;38;5;141m"
C_RED="\033[1;38;5;196m"
C_GRAY="\033[38;5;244m"
C_WHITE="\033[1;37m"
BG_BLUE="\033[48;5;21m"

while true; do
    NOW_WAT=$(date "+%d/%m/%Y %H:%M:%S (WAT - Douala)")
    NOW_ISO=$(date -u +"%Y-%m-%dT%H:%M:%SZ")

    clear

    # --- BANNIÈRE ASCII OFFICIELLE VIGILCORE ---
    echo -e "${C_BLUE}"
    echo "  ██╗   ██╗██╗ ██████╗ ██╗██╗      ██████╗ ██████╗ ██████╗ ███████╗"
    echo "  ██║   ██║██║██╔════╝ ██║██║     ██╔════╝██╔═══██╗██╔══██╗██╔════╝"
    echo "  ██║   ██║██║██║  ███╗██║██║     ██║     ██║   ██║██████╔╝█████╗  "
    echo "  ╚██╗ ██╔╝██║██║   ██║██║██║     ██║     ██║   ██║██╔══██╗██╔══╝  "
    echo "   ╚████╔╝ ██║╚██████╔╝██║███████╗╚██████╗╚██████╔╝██║  ██║███████╗"
    echo "    ╚═══╝  ╚═╝ ╚═════╝ ╚═╝╚══════╝ ╚═════╝ ╚═════╝ ╚═╝  ╚═╝╚══════╝"
    echo -e "${C_RESET}"
    echo -e "   ${C_BOLD}${C_WHITE}VIGILCORE ENTERPRISE${C_RESET} ${C_GRAY}•${C_RESET} ${C_CYAN}Incident & SLA Observability Suite${C_RESET} ${C_GRAY}•${C_RESET} ${C_GREEN}20 Services Hub${C_RESET}"
    echo -e "   ${C_GRAY}Hôte : ${C_WHITE}${HOST_NAME}${C_RESET} ${C_GRAY}| Fuseau Horaire : ${C_AMBER}Africa/Douala (WAT UTC+1)${C_RESET} ${C_GRAY}| Heure : ${C_WHITE}${NOW_WAT}${C_RESET}"
    echo -e "${C_GRAY}────────────────────────────────────────────────────────────────────────────────${C_RESET}"

    # --- CATÉGORIE 1 : PLATEFORMES MAVIANCE ---
    echo -e " ${C_BOLD}${C_CYAN}[1. PLATEFORMES MAVIANCE CORE]${C_RESET}"
    printf "   ${C_BLUE}[%2d]${C_RESET} %-36s ${C_BLUE}[%2d]${C_RESET} %-36s\n" \
      1 "Smobilpay Platform & APIs" \
      3 "Agent & Merchant Portal"
    printf "   ${C_BLUE}[%2d]${C_RESET} %-36s ${C_BLUE}[%2d]${C_RESET} %-36s\n" \
      2 "Third Party Merchant API (S3P)" \
      4 "Smobilpay for e-commerce"
    echo ""

    # --- CATÉGORIE 2 : MOBILE MONEY & TÉLÉCOMS ---
    echo -e " ${C_BOLD}${C_AMBER}[2. MOBILE MONEY & TELECOMS - CAMEROUN]${C_RESET}"
    printf "   ${C_AMBER}[%2d]${C_RESET} %-36s ${C_AMBER}[%2d]${C_RESET} %-36s\n" \
      5 "MTN Mobile Money (Général)" \
      10 "Orange Money : Retraits / Cashout"
    printf "   ${C_AMBER}[%2d]${C_RESET} %-36s ${C_AMBER}[%2d]${C_RESET} %-36s\n" \
      6 "Orange Money (Général)" \
      11 "MTN Recharge / Airtime"
    printf "   ${C_AMBER}[%2d]${C_RESET} %-36s ${C_AMBER}[%2d]${C_RESET} %-36s\n" \
      7 "MTN MoMo : Collections (Dépôts)" \
      12 "Orange Recharge / Airtime"
    printf "   ${C_AMBER}[%2d]${C_RESET} %-36s ${C_AMBER}[%2d]${C_RESET} %-36s\n" \
      8 "Orange Money : Collections" \
      13 "Camtel Recharge / Top-up"
    printf "   ${C_AMBER}[%2d]${C_RESET} %-36s\n" \
      9 "MTN MoMo : Retraits / Cashout"
    echo ""

    # --- CATÉGORIE 3 : FACTURIERS ÉNERGIE & EAU ---
    echo -e " ${C_BOLD}${C_GREEN}[3. FACTURES D'ENERGIE & D'EAU]${C_RESET}"
    printf "   ${C_GREEN}[%2d]${C_RESET} %-36s ${C_GREEN}[%2d]${C_RESET} %-36s\n" \
      14 "Factures ENEO (Électricité / Tokens)" \
      15 "Factures Camwater (Eau & Assainissement)"
    echo ""

    # --- CATÉGORIE 4 : RÉABONNEMENTS TV ---
    echo -e " ${C_BOLD}${C_PURPLE}[4. REABONNEMENTS TELEVISION]${C_RESET}"
    printf "   ${C_PURPLE}[%2d]${C_RESET} %-36s ${C_PURPLE}[%2d]${C_RESET} %-36s\n" \
      16 "Canal+ Télévision" \
      18 "StarTimes TV"
    printf "   ${C_PURPLE}[%2d]${C_RESET} %-36s\n" \
      17 "DSTV Télévision"
    echo ""

    # --- CATÉGORIE 5 : RÉGIONAL & ENTREPRISES ---
    echo -e " ${C_BOLD}${C_CYAN}[5. SERVICES REGIONAUX & PARTENAIRES]${C_RESET}"
    printf "   ${C_CYAN}[%2d]${C_RESET} %-36s ${C_CYAN}[%2d]${C_RESET} %-36s\n" \
      19 "MTN Mobile Money Congo" \
      20 "SABC Boissons (Paiements Marchands)"
    echo ""

    # --- PACKS DE DÉMONSTRATION SPÉCIAUX ---
    echo -e " ${C_BOLD}${C_WHITE}[6. SCENARIOS MULTI-SERVICES & DEMONSTRATION]${C_RESET}"
    printf "   ${C_AMBER}[%2d]${C_RESET} %-74s\n" \
      15 "PACK 15 SERVICES MAJEURS (Pannes combinées MoMo, Orange, ENEO, Canal+, S3P...)"
    printf "   ${C_RED}[%2d]${C_RESET} %-74s\n" \
      20 "BLACKOUT GLOBAL TOTAL (Simulation simultanée des 20 Passerelles Partenaires)"
    printf "   ${C_GREEN}[ r]${C_RESET} %-74s\n" \
      "RETABLIR TOUS LES SERVICES (Clôturer tous les incidents & Repasser à 20/20 Verts)"
    echo ""

    echo -e "   ${C_RED}[ 0]${C_RESET} ${C_GRAY}Annuler et Quitter (ou tapez 'q')${C_RESET}"
    echo -e "${C_GRAY}────────────────────────────────────────────────────────────────────────────────${C_RESET}"
    echo -ne " ${C_BOLD}${C_WHITE}> Entrez votre choix ${C_CYAN}(ex: 5 | 15 | 20 | r pour reset | 0/q pour quitter)${C_WHITE} : ${C_RESET}"
    read user_input

    # Nettoyage de la saisie
    user_input=$(echo "$user_input" | tr ',' ' ' | tr ';' ' ')

    if [ -z "$user_input" ] || [ "$user_input" = "0" ] || [ "$user_input" = "q" ] || [ "$user_input" = "Q" ] || [ "$user_input" = "exit" ]; then
        echo -e "\n ${C_AMBER}Session de simulation terminée. À bientôt !${C_RESET}\n"
        exit 0
    fi

    # Option Rétablissement / Reset de tous les incidents
    if [ "$user_input" = "r" ] || [ "$user_input" = "R" ] || [ "$user_input" = "reset" ] || [ "$user_input" = "clean" ]; then
        echo ""
        echo -e "${C_BOLD}${C_GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${C_RESET}"
        echo -e " ${C_BOLD}${C_GREEN}[RESOLUTION ET CLOTURE DE TOUS LES INCIDENTS EN COURS...]${C_RESET}"
        echo -e "${C_GRAY}────────────────────────────────────────────────────────────────────────────────${C_RESET}"
        php artisan vigilcore:reset-active-incidents
        echo -e "\n ${C_BOLD}${C_GREEN}[OK] SUCCES : Tous les 20 services sont maintenant 100% OPERATIONNELS (20/20 Verts) !${C_RESET}"
        echo -e " ${C_GRAY}Consultez le Dashboard : ${C_CYAN}${DASHBOARD_URL}${C_RESET}"
        echo -e "${C_BOLD}${C_GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${C_RESET}\n"
        
        echo -ne " ${C_BOLD}${C_WHITE}> Appuyez sur [Entrée] pour revenir au menu ou [0 / q] pour quitter : ${C_RESET}"
        read continue_choice
        if [ "$continue_choice" = "0" ] || [ "$continue_choice" = "q" ] || [ "$continue_choice" = "Q" ] || [ "$continue_choice" = "exit" ]; then
            echo -e "\n ${C_AMBER}Session terminée.${C_RESET}\n"
            exit 0
        fi
        continue
    fi

    # Gestion des sélections spéciales
    if [ "$user_input" = "15" ] || [ "$user_input" = "95" ] || [ "$user_input" = "1-15" ]; then
        SELECTED_SERVICES=(1 2 3 4 5 6 7 8 11 12 13 14 15 16 17)
        echo -e "\n${C_BOLD}${C_AMBER}[ACTIVATION DU PACK 15 SERVICES MAJEURS - 15 Passerelles Selectionnees]${C_RESET}"
    elif [ "$user_input" = "20" ] || [ "$user_input" = "99" ] || [ "$user_input" = "all" ] || [ "$user_input" = "1-20" ]; then
        SELECTED_SERVICES=(1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20)
        echo -e "\n${C_BOLD}${C_RED}[ACTIVATION DU BLACKOUT GLOBAL - 20 PASSERELLES EN SIMULATION SIMULTANEE]${C_RESET}"
    else
        SELECTED_SERVICES=($user_input)
    fi

    TOTAL_COUNT=${#SELECTED_SERVICES[@]}
    CURRENT_INDEX=1

    echo ""
    echo -e "${C_BOLD}${C_WHITE}[INFO] DEMARRAGE DU PROTOCOLE DE TELEMETRIE POUR ${C_CYAN}${TOTAL_COUNT} SERVICE(S)${C_WHITE}...${C_RESET}"
    echo -e "${C_GRAY}════════════════════════════════════════════════════════════════════════════════${C_RESET}"

    # Tableaux de collecte pour le bilan final
    REPORT_NAMES=()
    REPORT_ERRORS=()
    REPORT_HTTP=()
    REPORT_HASHES=()
    REPORT_STATUSES=()

# Fonction de traitement pédagogique et technique d'un service
process_service() {
    local choice=$1
    local idx=$2
    local total=$3

    KEY=""
    NAME=""
    TITLE=""
    ERR_CODE="ERR_GATEWAY_TIMEOUT_504"
    HTTP_CODE=504
    SEV="CRITICAL"
    LATENCY=$((RANDOM % 1500 + 2200)) # 2200ms à 3700ms en panne
    ROOT_CAUSE="Délai d'attente dépassé (Timeout 504) sur la passerelle partenaire."
    MSG_INV=""
    MSG_ID=""
    MSG_MON=""
    MSG_RES=""

    case $choice in
      1)
        KEY="smobilpay"
        NAME="Smobilpay Platform & APIs"
        TITLE="Perturbation Plateforme Smobilpay"
        ERR_CODE="ERR_GATEWAY_TIMEOUT_504"
        HTTP_CODE=504
        SEV="CRITICAL"
        ROOT_CAUSE="Saturation des workers PHP-FPM et timeout 504 lors de la validation des paiements."
        MSG_INV="Chers partenaires, nous constatons actuellement des ralentissements inhabituels lors de la validation de certains paiements sur la plateforme Smobilpay. Nos équipes techniques sont immédiatement mobilisées."
        ;;
      2)
        KEY="s3p"
        NAME="Third Party Merchant API (S3P)"
        TITLE="Instabilité API Marchand S3P"
        ERR_CODE="ERR_AUTH_TOKEN_EXPIRED_401"
        HTTP_CODE=401
        SEV="WARNING"
        ROOT_CAUSE="Expiration de jeton d'authentification inter-systèmes et rejet des appels intégrateurs."
        MSG_INV="Chers intégrateurs et marchands, des délais de réponse allongés sont relevés sur l'API S3P. Les équipes d'astreinte interviennent."
        ;;
      3)
        KEY="merchant_portal"
        NAME="Agent & Merchant Portal"
        TITLE="Lenteurs Portail Marchand"
        ERR_CODE="ERR_HIGH_LATENCY_3800MS"
        HTTP_CODE=200
        SEV="WARNING"
        ROOT_CAUSE="Verrouillage temporaire de table MySQL provoquant une latence de 3800ms sur le portail."
        MSG_INV="Chers agents et marchands, l'accès à votre espace de gestion connaît des lenteurs temporaires. Données et soldes sécurisés."
        ;;
      4)
        KEY="ecommerce"
        NAME="Smobilpay for e-commerce"
        TITLE="Perturbation Module E-Commerce"
        ERR_CODE="ERR_GATEWAY_TIMEOUT_504"
        HTTP_CODE=504
        SEV="CRITICAL"
        ROOT_CAUSE="Délai de réponse dépassé sur le webhook d'encaissement e-commerce."
        MSG_INV="Chers partenaires e-commerce, des échecs intermittents sont observés lors de la finalisation des paiements en ligne."
        ;;
      5)
        KEY="mtn_momo"
        NAME="MTN Mobile Money (Général)"
        TITLE="Perturbation Réseau MTN MoMo"
        ERR_CODE="ERR_INTERNAL_SERVER_500"
        HTTP_CODE=500
        SEV="CRITICAL"
        ROOT_CAUSE="Erreur interne HTTP 500 renvoyée par le commutateur USSD / API de MTN Cameroun."
        MSG_INV="Chers clients et partenaires, des instabilités temporaires touchent actuellement les opérations MTN Mobile Money."
        ;;
      6)
        KEY="orange_money"
        NAME="Orange Money (Général)"
        TITLE="Perturbation Réseau Orange Money"
        ERR_CODE="ERR_GATEWAY_TIMEOUT_504"
        HTTP_CODE=504
        SEV="CRITICAL"
        ROOT_CAUSE="Saturation de la passerelle Orange Money et dépassement du délai de garde (504 Timeout)."
        MSG_INV="Chers utilisateurs, des lenteurs sont signalées sur le réseau Orange Money. Vérifications en cours avec l'opérateur."
        ;;
      7)
        KEY="mtn_collection"
        NAME="MTN MoMo : Collections (Dépôts)"
        TITLE="Échecs Encaissements MTN MoMo"
        ERR_CODE="ERR_SERVICE_UNAVAILABLE_503"
        HTTP_CODE=503
        SEV="CRITICAL"
        ROOT_CAUSE="Point de terminaison /collection/v1 indisponible chez l'opérateur MTN (HTTP 503)."
        MSG_INV="Chers marchands, les encaissements via MTN MoMo rencontrent des rejets temporaires. Traitement prioritaire en cours."
        ;;
      8)
        KEY="orange_collection"
        NAME="Orange Money : Collections"
        TITLE="Échecs Encaissements Orange Money"
        ERR_CODE="ERR_SERVICE_UNAVAILABLE_503"
        HTTP_CODE=503
        SEV="CRITICAL"
        ROOT_CAUSE="Service d'encaissement marchand Orange momentanément inaccessible."
        MSG_INV="Chers partenaires marchands, les encaissements Orange Money subissent des interruptions partielles."
        ;;
      9)
        KEY="mtn_disbursement"
        NAME="MTN MoMo : Retraits / Cashout"
        TITLE="Blocage Retraits MTN MoMo"
        ERR_CODE="ERR_INSUFFICIENT_LIQUIDITY_422"
        HTTP_CODE=422
        SEV="WARNING"
        ROOT_CAUSE="Rejet 422 Unprocessable Entity : compte pool de distribution en réapprovisionnement."
        MSG_INV="Chers agents, les demandes de retrait MTN MoMo subissent des délais de traitement."
        ;;
      10)
        KEY="orange_disbursement"
        NAME="Orange Money : Retraits / Cashout"
        TITLE="Blocage Retraits Orange Money"
        ERR_CODE="ERR_INSUFFICIENT_LIQUIDITY_422"
        HTTP_CODE=422
        SEV="WARNING"
        ROOT_CAUSE="Synchronisation des quittances de débit différée sur l'API Cashout Orange."
        MSG_INV="Chers partenaires, les retraits d'argent via Orange Money connaissent des ralentissements."
        ;;
      11)
        KEY="mtn_airtime"
        NAME="MTN Recharge / Airtime"
        TITLE="Échec Recharges Téléphoniques MTN"
        ERR_CODE="ERR_TELCO_GATEWAY_DOWN_502"
        HTTP_CODE=502
        SEV="WARNING"
        ROOT_CAUSE="Bad Gateway (HTTP 502) sur le connecteur de rechargement crédit de communication MTN."
        MSG_INV="Chers utilisateurs, les achats de crédit MTN connaissent des échecs intermittents."
        ;;
      12)
        KEY="orange_airtime"
        NAME="Orange Recharge / Airtime"
        TITLE="Échec Recharges Téléphoniques Orange"
        ERR_CODE="ERR_TELCO_GATEWAY_DOWN_502"
        HTTP_CODE=502
        SEV="WARNING"
        ROOT_CAUSE="Passerelle de rechargement temps d'antenne Orange injoignable."
        MSG_INV="Chers utilisateurs, les recharges téléphoniques Orange sont momentanément différées."
        ;;
      13)
        KEY="camtel"
        NAME="Camtel Recharge / Top-up"
        TITLE="Indisponibilité Recharge Camtel"
        ERR_CODE="ERR_GATEWAY_TIMEOUT_504"
        HTTP_CODE=504
        SEV="WARNING"
        ROOT_CAUSE="Délai d'attente dépassé sur le serveur de recharge Blue / Camtel."
        MSG_INV="Chers clients, les recharges et forfaits internet Camtel Blue connaissent des retards de distribution."
        ;;
      14)
        KEY="eneo"
        NAME="Factures ENEO (Électricité / Tokens)"
        TITLE="Indisponibilité Achat Tokens ENEO"
        ERR_CODE="ERR_ENEO_TOKEN_SERVER_504"
        HTTP_CODE=504
        SEV="CRITICAL"
        ROOT_CAUSE="Serveur STS de génération des codes de recharge prépayée ENEO non réactif (504 Timeout)."
        MSG_INV="Chers abonnés, l'achat de tokens prépayés et le règlement des factures ENEO sont perturbés."
        ;;
      15)
        KEY="camwater"
        NAME="Factures Camwater (Eau)"
        TITLE="Blocage Paiement Factures Camwater"
        ERR_CODE="ERR_BILLER_API_DOWN_503"
        HTTP_CODE=503
        SEV="CRITICAL"
        ROOT_CAUSE="API de consultation des bordereaux et factures Camwater hors ligne (HTTP 503)."
        MSG_INV="Chers clients, le service de paiement des factures d'eau Camwater est temporairement indisponible."
        ;;
      16)
        KEY="canal"
        NAME="Canal+ Télévision"
        TITLE="Échec Réabonnements Canal+"
        ERR_CODE="ERR_CPA_ACTIVATION_TIMEOUT_504"
        HTTP_CODE=504
        SEV="CRITICAL"
        ROOT_CAUSE="Timeout 504 lors de l'envoi des commandes de réactivation des cartes décodeur Canal+."
        MSG_INV="Chers abonnés, le réabonnement direct et le réarmement des images Canal+ subissent des lenteurs."
        ;;
      17)
        KEY="dstv"
        NAME="DSTV Télévision"
        TITLE="Perturbation Réabonnements DSTV"
        ERR_CODE="ERR_MULTICHOICE_GATEWAY_502"
        HTTP_CODE=502
        SEV="WARNING"
        ROOT_CAUSE="Erreur 502 de routage vers la plateforme MultiChoice DSTV Afrique Centrale."
        MSG_INV="Chers clients, les souscriptions aux bouquets DSTV sont momentanément retardées."
        ;;
      18)
        KEY="startimes"
        NAME="StarTimes TV"
        TITLE="Échec Réabonnements StarTimes"
        ERR_CODE="ERR_STARTIMES_API_503"
        HTTP_CODE=503
        SEV="WARNING"
        ROOT_CAUSE="Maintenance inopinée sur les API de gestion des droits d'accès StarTimes."
        MSG_INV="Chers abonnés, le service de paiement StarTimes TV est en cours de recalibrage technique."
        ;;
      19)
        KEY="mtn_congo"
        NAME="MTN Mobile Money Congo"
        TITLE="Perturbation Liaison Régionale Congo"
        ERR_CODE="ERR_CROSS_BORDER_LINK_504"
        HTTP_CODE=504
        SEV="WARNING"
        ROOT_CAUSE="Lenteurs sur la passerelle de paiement transfrontalière MTN MoMo Congo (Brazzaville)."
        MSG_INV="Chers partenaires régionaux, les transactions vers la République du Congo subissent des latences."
        ;;
      20)
        KEY="sabc"
        NAME="SABC Boissons (Paiements Marchands)"
        TITLE="Indisponibilité Paiements SABC"
        ERR_CODE="ERR_SABC_SETTLEMENT_500"
        HTTP_CODE=500
        SEV="CRITICAL"
        ROOT_CAUSE="Erreur 500 sur le module de rapprochement bancaire des livraisons Boissons du Cameroun."
        MSG_INV="Chers dépositaires et marchands, le paiement des commandes SABC subit une indisponibilité technique."
        ;;
      *)
        echo -e "${C_RED}Option inconnue : $choice (Ignorée)${C_RESET}"
        return
        ;;
    esac

    # Calcul de l'empreinte SHA-256 Forensique (Preuve inaltérable)
    RAW_STRING="${KEY}|${HTTP_CODE}|${ERR_CODE}|${NOW_ISO}|srv901529"
    SHA_HASH=$(echo -n "$RAW_STRING" | sha256sum | awk '{print $1}')

    # --- AFFICHAGE PÉDAGOGIQUE EN 4 ÉTAPES (STYLE ARCHITECTURE VIGILCORE) ---
    echo ""
    echo -e "${C_BOLD}${C_WHITE}┌── [${idx}/${total}] SERVICE CIBLÉ : ${C_CYAN}${NAME}${C_RESET} ${C_GRAY}(Clé : ${KEY})${C_RESET}"
    echo -e "${C_GRAY}│${C_RESET}"
    
    # Étape 1 : Sonde Synthétique Proactive
    echo -e "${C_GRAY}├──${C_RESET} ${C_BOLD}${C_GREEN}[ÉTAPE 1/4 : DÉTECTION PROACTIVE PAR SONDE SYNTHÉTIQUE (30s)]${C_RESET}"
    echo -e "${C_GRAY}│   ├─${C_RESET} Erreur Détectée : ${C_RED}${ERR_CODE}${C_RESET} (Code HTTP : ${C_RED}${HTTP_CODE}${C_RESET})"
    echo -e "${C_GRAY}│   ├─${C_RESET} Mesure de Latence : ${C_AMBER}${LATENCY} ms${C_RESET} (Seuil d'alerte : >1500 ms)"
    echo -e "${C_GRAY}│   └─${C_RESET} Diagnostic Racine : ${C_WHITE}${ROOT_CAUSE}${C_RESET}"
    
    # Étape 2 : Boîte Noire & Scellement SHA-256
    echo -e "${C_GRAY}├──${C_RESET} ${C_BOLD}${C_PURPLE}[ÉTAPE 2/4 : BOÎTE NOIRE FORENSIQUE & SCELLEMENT CRYPTOGRAPHIQUE]${C_RESET}"
    echo -e "${C_GRAY}│   ├─${C_RESET} Capture Traces : En-têtes HTTP bruts, Payload & Pile d'appels scellés"
    echo -e "${C_GRAY}│   └─${C_RESET} Sceau Cryptographique : ${C_PURPLE}SHA-256:${SHA_HASH:0:32}...${C_RESET} ${C_GREEN}(Force Probante SLA)${C_RESET}"
    
    # Étape 3 : FSM Coupe-Circuit & Base de Données
    echo -e "${C_GRAY}├──${C_RESET} ${C_BOLD}${C_BLUE}[ÉTAPE 3/4 : FSM COUPE-CIRCUIT & PERSISTANCE MYSQL/REDIS]${C_RESET}"
    echo -e "${C_GRAY}│   ├─${C_RESET} Transition FSM : ${C_GREEN}OPÉRATIONNEL${C_RESET} ➔ ${C_RED}${SEV} / PANNE${C_RESET} (Circuit Breaker Ouvert)"
    echo -e "${C_GRAY}│   └─${C_RESET} Horodatage UTC : ${C_WHITE}${NOW_ISO}${C_RESET}"
    
    # Étape 4 : Webhook n8n & Diffusion Multicanal
    echo -e "${C_GRAY}└──${C_RESET} ${C_BOLD}${C_AMBER}[ÉTAPE 4/4 : ORCHESTRATION N8N & DIFFUSION MULTICANAL (<5s)]${C_RESET}"
    echo -e "    ${C_GRAY}├─${C_RESET} Moteur d'Aiguillage : ${C_WHITE}n8n Automation Engine (Protocole HTTP POST)${C_RESET}"
    echo -e "    ${C_GRAY}├─${C_RESET} Endpoint Webhook : ${C_CYAN}/webhook/vigilcore-alert${C_RESET} ${C_GRAY}(Port 443 SSL)${C_RESET}"

    # Construction du Payload JSON officiel VigilCore
    JSON_PAYLOAD=$(cat <<EOF
{
  "service": "${KEY}",
  "service_name": "${NAME}",
  "title": "${TITLE}",
  "severity": "${SEV}",
  "http_code": ${HTTP_CODE},
  "error_code": "${ERR_CODE}",
  "latency_ms": ${LATENCY},
  "root_cause": "${ROOT_CAUSE}",
  "sha256_hash": "${SHA_HASH}",
  "server": "${HOST_NAME}",
  "timestamp": "${NOW_ISO}",
  "message": "${MSG_INV}"
}
EOF
)

    # Transmission Réseau avec curl (Requête HTTP POST vers n8n)
    HTTP_RESP=$(curl -s -w "\n%{http_code}" -X POST "$N8N_URL" \
      -H "Content-Type: application/json" \
      -d "$JSON_PAYLOAD" 2>/dev/null)

    HTTP_STATUS=$(echo "$HTTP_RESP" | tail -n 1)

    if [ "$HTTP_STATUS" = "200" ] || [ "$HTTP_STATUS" = "201" ] || [ "$HTTP_STATUS" = "204" ]; then
        echo -e "    ${C_GRAY}└─${C_RESET} Statut Distribution : ${C_GREEN}[SUCCÈS 200 OK]${C_RESET} — Alertes WhatsApp NOC & Status Page synchronisées !"
        REPORT_STATUSES+=("[OK]")
    else
        echo -e "    ${C_GRAY}└─${C_RESET} Statut Distribution : ${C_AMBER}[TRANSMIS HTTP ${HTTP_STATUS}]${C_RESET}"
        REPORT_STATUSES+=("[OK]")
    fi

    # Mémorisation pour le rapport final
    REPORT_NAMES+=("$NAME")
    REPORT_ERRORS+=("$ERR_CODE")
    REPORT_HTTP+=("$HTTP_CODE")
    REPORT_HASHES+=("${SHA_HASH:0:16}...")

    # Pause fluide pour la présentation
    if [ "$total" -gt 1 ]; then
        sleep 0.4
    fi
}

# Boucle de traitement de tous les services sélectionnés
for choice in "${SELECTED_SERVICES[@]}"; do
    process_service "$choice" "$CURRENT_INDEX" "$TOTAL_COUNT"
    CURRENT_INDEX=$((CURRENT_INDEX + 1))
done

# --- BILAN & TABLEAU RÉCAPITULATIF DE LA DÉMONSTRATION ---
echo ""
echo -e "${C_GRAY}════════════════════════════════════════════════════════════════════════════════${C_RESET}"
echo -e " ${C_BOLD}${C_GREEN}SYNTHESE DE LA SIMULATION : ${TOTAL_COUNT} SERVICES ENREGISTRES DANS VIGILCORE${C_RESET}"
echo -e "${C_GRAY}────────────────────────────────────────────────────────────────────────────────${C_RESET}"
printf " ${C_BOLD}%-3s | %-30s | %-8s | %-20s | %-12s${C_RESET}\n" "#" "Passerelle Partenaire" "HTTP" "Code Erreur" "Empreinte SHA"
echo -e "${C_GRAY}────┼────────────────────────────────┼──────────┼──────────────────────┼─────────────${C_RESET}"

for i in "${!REPORT_NAMES[@]}"; do
    NUM=$((i + 1))
    printf " %2d | %-30s | ${C_RED}%-8s${C_RESET} | ${C_AMBER}%-20s${C_RESET} | ${C_PURPLE}%-12s${C_RESET}\n" \
      "$NUM" "${REPORT_NAMES[$i]}" "${REPORT_HTTP[$i]}" "${REPORT_ERRORS[$i]}" "${REPORT_HASHES[$i]}"
done

echo -e "${C_GRAY}════════════════════════════════════════════════════════════════════════════════${C_RESET}"
echo -e " ${C_BOLD}${C_WHITE}PROTOCOLE DE DEMONSTRATION & SOUTENANCE :${C_RESET}"
echo -e "  1. Tableau de bord Live : ${C_CYAN}${DASHBOARD_URL}${C_RESET} (Surveillance en direct des 20 services)"
echo -e "  2. Inspection Forensique : Cliquez sur un incident pour voir le ${C_PURPLE}Payload JSON scellé par SHA-256${C_RESET}"
echo -e "  3. Rapports d'exploitation : Rendez-vous sur ${C_CYAN}https://vigilcore.calebdevs.com/reports${C_RESET} (Export PDF / Excel)"
echo -e "  4. Console n8n (Journal des Exécutions Live) : ${C_CYAN}${N8N_EXECUTIONS_URL}${C_RESET}"
echo -e "${C_GRAY}────────────────────────────────────────────────────────────────────────────────${C_RESET}\n"

    echo -ne " ${C_BOLD}${C_WHITE}> Appuyez sur [Entrée] pour effectuer une autre simulation ou [0 / q] pour quitter : ${C_RESET}"
    read next_action
    if [ "$next_action" = "0" ] || [ "$next_action" = "q" ] || [ "$next_action" = "Q" ] || [ "$next_action" = "exit" ]; then
        echo -e "\n ${C_AMBER}Session de simulation terminée. À bientôt !${C_RESET}\n"
        exit 0
    fi
done
