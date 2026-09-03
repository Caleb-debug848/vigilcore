# 📘 DOCUMENT DE RÉFÉRENCE & MASTER BRIEFING INTÉGRAL
## RAPPORT DE STAGE ACADÉMIQUE D'INGÉNIERIE LOGICIELLE — IAI-CAMEROUN

---

## 🏛️ 1. IDENTIFICATION ADMINISTRATIVE & ACADÉMIQUE

| Élément | Valeur Officielle Exacte |
| :--- | :--- |
| **Thème Officiel** | `CONCEPTION ET RÉALISATION D'UNE PLATEFORME DE SURVEILLANCE AUTOMATISÉE, D'ALERTE EN TEMPS RÉEL ET DE SUIVI DE LA DISPONIBILITÉ DES SERVICES DE PAIEMENT NUMÉRIQUE : CAS DE MAVIANCE` |
| **Nom de la Solution** | **VigilCore** *(Plateforme de supervision de passerelles de paiement, monitoring de sondes HTTP/TCP 30s, boîte noire forensique et certification d'audit SLA)* |
| **Auteur / Candidat** | **DASSI NZALI CALEB DAONY** |
| **Niveau & Filière** | Élève-Ingénieur en 3ᵉ année Génie Logiciel / Ingénieur des Travaux Informatiques |
| **Institution** | **Institut Africain d’Informatique (IAI-Cameroun)** — *Centre d’Excellence Technologique PAUL BIYA*, Yaoundé |
| **Représentant Résident IAI** | **M. Armand Claude ABANDA** |
| **Encadrant Académique** | **Mme BADJECK** *(Enseignante & Maître de conférences à l'IAI-Cameroun)* |
| **Entreprise d'Accueil** | **MAVIANCE PLC (Cameroun)** — Leader FinTech en zone CEMAC |
| **Localisation de l'Entreprise** | Siège Social : NFC Bank Building, Bonanjo, Douala – Cameroun |
| **Directeur Général (CEO)** | **Dr.-Ing Nkwenti AZONG-WARA** |
| **Directeur IT & Opérations** | **M. Socrates TCHUIATCHEU** |
| **Encadrant Professionnel** | **Mme EVA DANIELLE DJAMPA** *(Déléguée chargée des Opérations chez MAVIANCE)* |
| **Période Officielle du Stage** | Du **01 Juillet 2026 au 30 Septembre 2026** (3 mois / 13 semaines) |
| **Année Académique** | **2025 - 2026** |
| **Statut du Projet** | **Projet de Stage Académique** *(banc d'essai, simulation de pannes réelles et démonstration pour soutenance)* |

---

## 👨‍👩‍👧‍👦 2. DÉDICACE & REMERCIEMENTS PERSONNALISÉS

### A. Dédicace (Page II)
* **Forme** : Grande bulle bleue ronde centrée.
* **Texte exact** :
  > **À**  
  > **NOS PARENTS**

### B. Remerciements (Page III)
* **M. Armand Claude ABANDA**, Représentant Résident de l'IAI-Cameroun, pour son engagement continu dans la professionnalisation des jeunes dans les TIC.
* **Le corps professoral et administratif de l'IAI-Cameroun** pour l'encadrement académique durant tout le cursus.
* **Mme EVA DANIELLE DJAMPA**, encadrante professionnelle et maître de stage chez MAVIANCE (Déléguée chargée des Opérations), pour sa disponibilité, ses orientations méthodologiques et son suivi rigoureux.
* **Mme BADJECK**, encadrante académique, pour ses conseils méthodologiques et canevas d'ingénierie.
* **M. Socrates TCHUIATCHEU** (Directeur IT & Opérations de MAVIANCE PLC) pour son accueil bienveillant au sein des départements IT et Opérations.
* **L'ensemble des équipes techniques et opérationnelles de MAVIANCE PLC** pour leur collaboration et leur esprit d'équipe.
* **Mme ONGNESSEK BASSONG Hortense**, ma maman, pour ses conseils, son soutien moral, matériel et ses prières.
* **M. ASAPH Tchounga**, mon père, pour son soutien constant, ses sacrifices et ses encouragements.
* **Mme JEANETTE Tchounga**, ma grand-mère, pour son affection et ses bénédictions.
* **Mme DAMARIS Yolande**, ma tante, pour son soutien régulier et ses encouragements.
* **M. TAGHUN Éric** et **Mme LOISE MBIANDJI** pour leur assistance, écoute et soutien indéfectible.

---

## 🏢 3. 1ère PARTIE : PHASE D’INSERTION & PRÉSENTATION DE MAVIANCE

### A. Accueil et Intégration
* **Accueil le 01/07/2026 à 8h00** au siège social de MAVIANCE (NFC Bank Building, Bonanjo, Douala) par notre encadrante professionnelle **Mme EVA DANIELLE DJAMPA** (Déléguée chargée des Opérations chez MAVIANCE).
* Visite des installations, présentation de la culture d'entreprise (*Customer First, Intégrité, Innovation, Excellence*).
* Intégration au sein des équipes du département unifié **IT & Operations** placé sous la direction de **M. Socrates TCHUIATCHEU** (Directeur IT & Opérations) et la coordination de **Mme Eva Danielle DJAMPA**.
* Onboarding technique : étude des flux de paiement Smobilpay, interopérabilité régionale GIMAC, protocoles de communication avec les opérateurs partenaires.

### B. Fiche d'Identité et Historique de MAVIANCE PLC
* **Fondation** : Créée en **2012**, MAVIANCE PLC est une société de technologie financière (FinTech) devenue l'acteur de référence dans la digitalisation des paiements en Afrique Centrale.
* **Agrégateur Régional GIMAC** : Partenaire technologique officiel du Groupement Interbancaire Monétique de l'Afrique Centrale pour l'interopérabilité bancaire et mobile money dans les 6 pays membres de la CEMAC.
* **Chiffres Clés** :
  * **+2 millions de transactions financières mensuelles**.
  * **+300 milliards FCFA** de flux financiers annuels traités.
  * **+10 millions d'utilisateurs uniques** via la plateforme Smobilpay.
  * Réseau de distribution de **+10 000 agents et points de vente SmartCash**.
  * Interconnexion directe avec **+150 banques, microfinances et émetteurs de monnaie électronique**.

### C. Organigramme Hiérarchique de MAVIANCE
* **Board (Conseil d'Administration)** : Organe de décision stratégique.
* **CEO (Directeur Général)** : Dr.-Ing Nkwenti AZONG-WARA.
* **Directions Opérationnelles** :
  1. **IT & OPERATIONS** — M. Socrates TCHUIATCHEU *(Directeur IT & Opérations)*, secondé par Mme Eva Danielle DJAMPA *(Déléguée chargée des Opérations)*.
  2. **CCO (Direction Commerciale)** — M. Franck YANOU *(Paiements de masse, Réseau Agents, B2B)*.
  3. **CFO (Direction Financière)** — Mme Clara NGANDO *(Comptabilité, Trésorerie, Fiscalité)*.
  4. **CMO (Marketing & Communication)** — M. David EKWABI.
  5. **HRD (Ressources Humaines)** — M. Ogbe ABUNAW *(Recrutement, Formation, Éthique)*.
  6. **DSP (Projets Stratégiques)** — Mme Sorele WABO.

### D. Partenaires Majeurs Interconnectés
1. **BEAC & GIMAC** : Compensation et interopérabilité monétique sous-régionale.
2. **Orange Cameroun (Orange Money)** : Passerelles API Cash-In, Cash-Out et transferts.
3. **MTN Cameroon (MTN Mobile Money)** : Passerelles MoMo API de paiement et collecte.
4. **ENEO Cameroun** : Achat d'énergie prépayée (compteurs intelligents) et règlement de factures.
5. **DGI (Direction Générale des Impôts)** : Télépaiement des impôts, droits et taxes d'État.
6. **Canal+ Cameroun & CamTel** : Réabonnement aux bouquets TV et recharge télécoms.

---

## ⚙️ 4. 2ème PARTIE : PHASE TECHNIQUE (LES 7 DOSSIERS OFFICIELS)

---

### 📂 DOSSIER 1 : L'EXISTANT & PROBLÉMATIQUE

#### 1. Description du Système Existant
La supervision des 20 passerelles de paiement interconnectées à la plateforme Smobilpay reposait historiquement sur un modèle **réactif** :
* Les coupures de service étaient principalement signalées par les réclamations des marchands ou clients finaux.
* Les alertes étaient transmises manuellement sur des groupes informels par les opérateurs support.
* Absence d'archivage automatique des en-têtes et corps d'erreurs HTTP 500/504 (logs dispersés ou écrasés).

#### 2. Tableau Critique de l'Existant

| Critiques | Conséquences Opérationnelles | Solutions Apportées par VigilCore |
| :--- | :--- | :--- |
| **Détection tardive des pannes** | Délais de constatation > 15 à 30 minutes ; transactions bloquées chez les marchands. | **Sondes autonomes 30s** interrogeant les 20 passerelles en continu sans intervention humaine. |
| **Alertes manuelles dispersées** | Risque élevé d'oubli nocturne ; transmission lente aux ingénieurs d'astreinte. | **Orchestration n8n** avec diffusion d'alertes instantanées par message **WhatsApp en < 5 secondes**. |
| **Litiges contractuels sur les SLA** | Incapacité à justifier précisément les temps d'arrêt causés par les serveurs tiers face aux partenaires. | **Générateur de registre d'audit 12 colonnes certifié par empreinte SHA-256** opposable aux tiers. |
| **Perte des traces forensiques** | Diagnostic difficile a posteriori de la cause exacte du crash partenaire. | **Module Boîte Noire Diagnostic** archivant l'état brut (URL, payload JSON, HTTP 500/504). |

#### 3. Problématique Centrale
> *« Comment concevoir et développer une plateforme capable de surveiller de manière proactive la disponibilité de 20 services partenaires de paiement numérique, d'alerter instantanément les équipes de maintenance et de certifier avec précision les temps d'arrêt pour garantir le respect rigoureux des accords de niveau de service (SLA) ? »*

---

### 📂 DOSSIER 2 : CAHIER DES CHARGES & PLANIFICATION

#### 1. Objectifs du Projet
* **Objectif Général** : Mettre sur pied la solution logicielle **VigilCore** pour automatiser la surveillance de 20 passerelles de paiement, diffuser les alertes d'incidents et certifier les métriques SLA.
* **Objectifs Spécifiques** :
  1. Automatiser l'exécution de sondes de santé HTTP/TCP toutes les 30 secondes.
  2. Implémenter un Dashboard temps réel réactif avec voyants d'état dynamique.
  3. Mémoriser les pannes dans une boîte noire forensique (requêtes, headers, réponses d'erreur).
  4. Diffuser les alertes en temps réel via des webhooks et l'Passerelle WhatsApp (Evolution API) orchestrés par n8n.
  5. Calculer automatiquement les métriques contractuelles : Uptime (%), MTTR (Mean Time to Recovery), Taux d'indisponibilité.
  6. Produire un registre d'audit certifié 12 colonnes exportable en Excel (.xlsx) et PDF avec signature cryptographique SHA-256.

#### 2. Matrice des Besoins Fonctionnels (F1 à F10)

| Réf. | Besoin Fonctionnel | Description Détaillée |
| :--- | :--- | :--- |
| **F1** | **Exécution des Sondes 30s** | Requêtes HTTP GET/POST automatiques toutes les 30s vers les 20 passerelles avec détection de timeout configurable (> 5000ms). |
| **F2** | **Détection de Coupure** | Qualification de la panne après 3 échecs consécutifs pour éviter les faux positifs réseau. |
| **F3** | **Tableau de Bord Live** | Dashboard temps réel avec code couleur : 🟢 Opérationnel, 🟡 Dégradé, 🔴 En Panne, ⚪ En Maintenance. |
| **F4** | **Boîte Noire Diagnostic** | Capture et archivage JSONB des en-têtes HTTP, code statut (500, 502, 503, 504), URL et payload d'erreur. |
| **F5** | **Diffusion Multi-Canal** | Déclenchement de webhooks vers n8n pour notification immédiate WhatsApp de l'équipe de garde. |
| **F6** | **Page Publique d'État** | Status Page consultable par les partenaires affichant la disponibilité en direct sans accès admin. |
| **F7** | **Moteur de Calcul SLA** | Calcul en temps réel de la disponibilité mensuelle (%) et de la durée cumulée d'interruption. |
| **F8** | **Génération Registre 12 Col.** | Production du registre d'audit complet (Date, Service, Incident, Début, Fin, Durée, Statut, Code HTTP, MTTR, SLA Impact, Empreinte SHA-256, Responsable). |
| **F9** | **Exportation Multi-Format** | Téléchargement direct des rapports d'audit au format Microsoft Excel (.xlsx) via PhpSpreadsheet et PDF. |
| **F10** | **Traçabilité & Sécurité RBAC** | Journalisation de toutes les actions (acquittement, clôture manuelle) avec contrôle d'accès basé sur les rôles. |

#### 3. Planification du Projet (Diagramme de Gantt sur 13 Semaines)
* **Démarche retenue** : **2TUP (Two Tracks Unified Process)** du 01/07/2026 au 30/09/2026.
* **Ordonnancement des 9 phases** :
  1. **INSERTION EN ENTREPRISE** (01/07 – 14/07, 14 j) — `[Caleb, Mme Djampa]`
  2. **cahier de charge & spécifications** (15/07 – 28/07, 14 j) — `[Caleb, Équipe Projet]`
  3. **Analyse fonctionnelle (2TUP)** (29/07 – 15/08, 18 j) — `[Caleb, Mme Badjeck]`
  4. **Montage & Conception de la solution** (16/08 – 31/08, 16 j) — `[Caleb]`
  5. **le systeme backend (Laravel 12 / n8n)** (01/09 – 12/09, 12 j) — `[Caleb]`
  6. **Planning Prévisionnel du Projet** (13/09 – 19/09, 7 j) — `[Caleb, Mme Badjeck]`
  7. **Creation Dashboard Livewire & Boîte Noire** (20/09 – 25/09, 6 j) — `[Caleb]`
  8. **test global & simulation pannes** (26/09 – 30/09, 5 j) — `[Caleb, Équipe QA]`
  9. **redaction continue du rapport de stage** (15/07 – 30/09, 88 j) — `[Caleb, Mme Badjeck]`

#### 4. Budget Évalué du Projet (en FCFA)
* **Ressources Matérielles** (PC dev Core i7 16Go SSD, Serveur de test, Onduleur, Connexion Fibre) : **950 000 FCFA**.
* **Ressources Logicielles & Cloud** (Hébergement VPS, Domaine, Certificat SSL, Passerelle WhatsApp (Evolution API)) : **341 250 FCFA**.
* **Ressources Humaines & Ingénierie** (Indemnité stagiaire-ingénieur, supervision technique 3 mois) : **750 000 FCFA**.
* **Imprévus & Contingences (10 %)** : **204 125 FCFA**.
* **TOTAL GÉNÉRAL DU PROJET** : **2 245 375 FCFA TTC** *(Deux millions deux cent quarante-cinq mille trois cent soixante-quinze Francs CFA)*.

---

### 📂 DOSSIER 3 : DOSSIER D'ANALYSE (2TUP & UML 2.5 SOUS POWERAMC)

#### 1. Méthodologie et Étude Comparative
* **UML vs MERISE** :
  * *MERISE* : Séparation stricte données/traitements (MCD/MOT). Trop rigide pour des architectures événementielles web temps réel.
  * *UML 2.5* : Modélisation orientée objet, unifiant structure et comportement. Parfaitement adapté au framework Laravel 12 et aux microservices.
* **Processus 2TUP** : Cycle en Y avec branche fonctionnelle (analyse des besoins et cas d'utilisation) et branche technique (architecture et technologies) convergeant vers la conception.

#### 2. Modélisation des Cas d'Utilisation (Conformité UML 2.5 & Directives Académiques)
* **Acteurs Humains (À gauche)** :
  * `Opérateur / Collaborateur de Supervision` (Surveiller en direct, simuler des pannes sur banc d'essai, consulter la boîte noire et suivre la résolution automatisée des incidents).
  * `Utilisateur Interne (Tout collaborateur connecté / Responsable SLA / Direction)` (Consulter le dashboard, **générer et télécharger les rapports d'audit SLA** — ouvert à tout utilisateur interne connecté).
  * `Administrateur Système` (Configurer les seuils de sondes et les passerelles).
  * `Partenaire Marchand / Client Externe` (Consulter la page publique d'état Statuspage).
* **Acteurs Systèmes Tiers Externes (À droite - Pas d'outils techniques internes comme acteurs)** :
  * `Passerelles Partenaires` (Orange, MTN, ENEO, Camwater...) : Reçoivent les sondes et renvoient les statuts.
  * `Passerelle WhatsApp (Evolution API)` : Reçoit les requêtes d'envoi et délivre les alertes mobiles.
* **Cas d'Utilisation Synthétiques & Règles d'Inclusion** :
  * Regroupement des micro-actions dans une bulle majeure : **« Générer et exporter les rapports d'audit SLA (Excel / PDF) »**.
  * Pas d'enchaînement séquentiel temporel par `«include»` (les étapes appartiennent aux diagrammes d'activité et de séquence).

#### 3. Fiches Textuelles Formelles IAI (Canevas Standard)
* Chaque cas d'utilisation est documenté avec : *Titre, Résumé, Acteurs (Primaires/Secondaires), Préconditions, Scénario Nominal (étapes numérotées 1 à 6), Scénarios Alternatifs, Exceptions/Erreurs, Post-conditions de succès et d'échec, Exigences Non-Fonctionnelles*.

#### 4. Diagrammes Dynamiques Modélisés
* **Diagrammes de Séquence & d'Activité** :
  * Découpage rigoureux par couloirs d'activité (Swimlanes).
  * Mise en évidence formelle du **calcul automatique en tâche de fond (Background Worker asynchrone)** de la durée de panne, du MTTR et du taux de disponibilité consolidé dès qu'un incident passe à l'état `Résolu`.

---

### 📂 DOSSIER 4 : DOSSIER DE CONCEPTION (MODÉLISATION POWERAMC)

#### 1. Diagramme de Classes Métier (7 Classes Maîtresses)
1. **`Utilisateur`** : Possède les méthodes de demande (`+ demanderRapportAudit(periode)`) sans héberger le code de génération de fichier.
2. **`ServicePartenaire`** : `code_service`, `nom_service`, `categorie`, `adresse_test_sante`, `statut_actuel`, `taux_disponibilite`.
3. **`BoiteNoireDiagnostic`** : Reliée à `Incident` par une **Composition Forte (Losange Noir Plein `◆`)** car sa durée de vie dépend strictement de l'incident.
4. **`Incident`** : `id`, `titre`, `service_concerne`, `degre_gravite`, `etat_avancement`, `date_declenchement`, `date_resolution`, `donnees_brutes (JSONB)`.
5. **`NotificationAlerte`** : Reliée à `Incident` par composition (`◆`).
6. **`RapportAuditDisponibilite`** : Héberge les méthodes métier réelles : `+ genererRegistreExcel12Col()`, `+ genererFichePdfPaysage()`, `+ calculerTauxDisponibilite()`, `+ certifierRapport()`.
   * **Multiplicité Normalisée** : **`Incident (1..*) <=====> (0..*) RapportAuditDisponibilite`** (Un rapport consolide 0 à plusieurs incidents ; un même incident peut figurer dans plusieurs rapports d'audit).
7. **`ScenarioSimulation`** : Catalogue de tests sur banc d'essai.

#### 2. Diagrammes d'États-Transitions
* **Règle UML 2.5** : Toutes les transitions d'état sont dessinées en **TRAIT PLEIN** (y compris la transition d'annulation d'une fausse alerte `annulationFausseAlerte` et de retour/rechute).
* Cycle de vie de l'incident : `En cours d'analyse` ➔ `Cause Racine Identifiée` ➔ `Surveillance & Tests` ➔ `Résolu & Rétabli` ➔ `Archivé & Audité`.

#### 3. Diagramme de Paquetages
* Architecture découpée en 4 packages : `Presentation_UI` (Livewire/Blade), `Business_Logic` (Monitoring & SLA Services), `Data_Access` (Eloquent Models & Migrations), `External_Integrations` (APIs Partenaires, n8n, WhatsApp).

---

### 📂 DOSSIER 5 : DOSSIER DE RÉALISATION & ARCHITECTURE TECHNIQUE

#### 1. Ressources Logicielles & Outils Utilisés

| Catégorie | Outil / Logiciel | Version | Rôle & Utilité dans le Projet |
| :--- | :--- | :--- | :--- |
| **Langage & Framework Backend** | **PHP** / **Laravel** | PHP 8.3 / Laravel 12 | Cœur applicatif MVC, routage API, injection de dépendances, scheduler CRON (sondes 30s) et queues asynchrones. |
| **Framework Frontend Réactif** | **Livewire** / **Alpine.js** | Livewire 3.x / Alpine 3 | Rendu dynamique du Dashboard en temps réel avec mise à jour intelligente des composants sans rechargement. |
| **Style & Design UI** | **Tailwind CSS** | Tailwind v3.4 | Conception de l'interface moderne, responsive, avec thèmes clair/sombre et codes couleurs d'état (vert/jaune/rouge). |
| **Moteur d'Orchestration** | **n8n Workflow Engine** | n8n v1.x (Docker) | Automatisation des flux d'alertes événementielles, réception des webhooks de pannes et formatage des messages WhatsApp. |
| **Base de Données Principale** | **PostgreSQL** | PostgreSQL 16 | Stockage relationnel haute performance avec support des colonnes `JSONB` pour la boîte noire forensique. |
| **Générateur Tableurs d'Audit** | **PhpSpreadsheet** | v2.x | Création automatisée des registres d'audit 12 colonnes au format Excel (.xlsx) avec formules SLA et SHA-256. |
| **Passerelle d'Alertes Mobiles** | **Evolution API (Passerelle WhatsApp)** | Evolution API v2.x (Docker) | Envoi instantané des notifications critiques aux téléphones de garde et ingénieurs d'astreinte (< 5 secondes). |
| **Modélisation & Conception** | **SAP ** |  v16.5 | Conception rigoureuse de tous les diagrammes UML 2.5 (Use Cases, Séquences, Activités, Classes, États, Paquetages). |
| **Planification de Projet** | **GanttProject** | GanttProject 3.3 | Élaboration du diagramme de Gantt officiel sur 13 semaines selon le cycle en deux branches du processus 2TUP. |
| **Gestionnaire de Version** | **Git** / **GitHub** | Git 2.4x | Suivi des versions du code source, branches de fonctionnalités et historique des commits du projet. |
| **Serveur Web & Reverse Proxy** | **Nginx** / **PHP-FPM** | Nginx 1.24 | Serveur web haute disponibilité gérant les connexions sécurisées HTTPS/TLS 1.3 et la compression Gzip. |
| **Tests & Client API** | **Postman** / **cURL** | v10.x | Simulation manuelle des pannes, injection de requêtes corrompues (HTTP 500/504) et validation des sondes. |

#### 2. Matrice Complète des 20 Services Partenaires Surveillés

| N° | Clé Système | Nom Officiel du Service | Catégorie Métier | Protocole / Type de Sonde | SLA Cible |
| :---: | :--- | :--- | :--- | :--- | :---: |
| **1** | `smobilpay` | **Smobilpay Platform & APIs** | Plateforme Centrale Maviance | Sonde HTTP GET `/health` & Latence | **99.95 %** |
| **2** | `s3p` | **Third Party Merchant API (S3P)** | Passerelle B2B Marchands | Sonde HTTP POST `/api/v2/status` | **99.90 %** |
| **3** | `merchant_portal` | **Agent & Merchant Portal** | Portail de Gestion Agents | Sonde HTTP GET `/portal/ping` | **99.90 %** |
| **4** | `ecommerce` | **Smobilpay for e-Commerce** | Module Paiement Web/Online | Sonde HTTP POST `/checkout/health` | **99.90 %** |
| **5** | `mtn_momo` | **MTN Mobile Money (Général)** | Mobile Money & Télécoms | API MoMo Gateway / Handshake TLS | **99.90 %** |
| **6** | `orange_money` | **Orange Money (Général)** | Mobile Money & Télécoms | API Orange Money / Heartbeat | **99.90 %** |
| **7** | `mtn_collection` | **MTN MoMo : Collections (Cash-In)** | Encaissement Marchand MoMo | Sonde HTTP POST `/collection/ping` | **99.90 %** |
| **8** | `orange_collection` | **Orange Money : Collections (Cash-In)** | Encaissement Marchand Orange | Sonde HTTP POST `/om/collection/ping` | **99.90 %** |
| **9** | `mtn_disbursement` | **MTN MoMo : Disbursement (Cash-Out)** | Transferts Sortants & Retraits | Sonde HTTP POST `/disbursement/check` | **99.90 %** |
| **10** | `orange_disbursement`| **Orange Money : Disbursement (Cash-Out)** | Transferts Sortants & Retraits | Sonde HTTP POST `/om/disburse/check` | **99.90 %** |
| **11** | `mtn_airtime` | **MTN Recharge / Airtime** | Recharges Télécoms | Sonde TCP/HTTP `/airtime/mtn` | **99.85 %** |
| **12** | `orange_airtime` | **Orange Recharge / Airtime** | Recharges Télécoms | Sonde TCP/HTTP `/airtime/orange` | **99.85 %** |
| **13** | `camtel` | **Camtel Recharge / Top-up Blue** | Recharges Télécoms | Sonde HTTP GET `/topup/camtel` | **99.80 %** |
| **14** | `eneo` | **Factures ENEO (Électricité / Prépayé)** | Factures Énergie & Utilitaires | API ENEO Token & Quittances | **99.90 %** |
| **15** | `camwater` | **Factures Camwater (Eau)** | Factures Eau & Utilitaires | Passerelle Règlements Camwater | **99.80 %** |
| **16** | `canal` | **Canal+ Télévision** | Réabonnements TV & Médias | API Canal+ Réactivation Décodeurs | **99.90 %** |
| **17** | `dstv` | **DSTV Télévision** | Réabonnements TV & Médias | API MultiChoice / DSTV Activation | **99.85 %** |
| **18** | `startimes` | **StarTimes TV** | Réabonnements TV & Médias | Passerelle Cartes StarTimes | **99.80 %** |
| **19** | `mtn_congo` | **MTN Mobile Money Congo** | Interopérabilité Régionale CEMAC | Couloir Transfrontalier MoMo Congo | **99.80 %** |
| **20** | `sabc` | **Boissons du Cameroun (SABC)** | Distribution & Règlements B2B | API Validation Commandes SABC | **99.85 %** |

#### 3. Architecture Physique & Déploiement
* **Architecture 3-Tiers Distribuée** :
  * Client : Navigateur Web (Dashboard live).
  * Serveur Applicatif : Nginx + PHP-FPM (Laravel 12) + Worker n8n.
  * Serveur Données : Instance PostgreSQL 16 sécurisée avec sauvegardes automatisées.
  * Réseau Partenaire Externe : Connexions sortantes HTTPS chiffrées en TLS 1.3 vers les 20 passerelles de paiement.

---

### 📂 DOSSIER 6 : TESTS DE FONCTIONNALITÉS & RÉSULTATS

#### 1. Cycle de Qualification en 5 Étapes & Détection Multi-Services

Le système applique un cycle formel en 5 étapes avant et pendant chaque incident :
1. **Étape 1 [Détection]** : Sondes HTTP toutes les 30s sur les 20 endpoints partenaires (détection de micro-coupures et timeouts).
2. **Étape 2 [Qualification]** : Typage automatique selon le code d'erreur HTTP (`504 Gateway Timeout`, `500 Server Error`, `503 Unavailable`, `401 Unauthorized`) et calcul de la sévérité (`CRITICAL` vs `WARNING`).
3. **Étape 3 [Boîte Noire Forensique]** : Archivage instantané du payload JSON brut, des en-têtes et de l'horodatage WAT (Douala) dans PostgreSQL et Elasticsearch.
4. **Étape 4 [Diffusion Multi-Canal]** : Webhook vers l'orchestrateur n8n ➔ Alerte instantanée WhatsApp via Evolution API aux ingénieurs d'astreinte (< 5s) + Publication graduée sur la Statuspage publique.
5. **Étape 5 [Résolution & Calcul SLA]** : Clôture dès confirmation de 3 succès consécutifs (200 OK), calcul automatique du MTTR (durée exacte en secondes) et scellement dans le registre 12 colonnes.

#### 2. Campagne de Tests sur Banc d'Essai (Mono et Multi-Services)

| Test | Scénario & Procédure | Résultat Constaté | Statut |
| :--- | :--- | :--- | :--- |
| **Test 1 : Sondes 30s** | Requêtes HTTP toutes les 30s sur 20 endpoints simulés. | 100 % des requêtes exécutées ; temps de réponse moyen : 142 ms. | ✅ Conforme |
| **Test 2 : Simulation Panne** | Coupure provoquée sur la passerelle MTN Mobile Money (HTTP 500). | Détection automatique après 3 échecs ; voyant rouge activé en 28 secondes. | ✅ Conforme |
| **Test 3 : Boîte Noire** | Injection d'un payload corrompu avec timeout 504 Gateway. | Archivage complet des en-têtes et du corps d'erreur dans PostgreSQL en 12 ms. | ✅ Conforme |
| **Test 4 : Alerte WhatsApp** | Déclenchement du Webhook d'incident vers n8n. | Notification WhatsApp reçue sur smartphone d'astreinte en **3.4 secondes** via Evolution API. | ✅ Conforme |
| **Test 5 : Simulation Multi-Pannes (6 Services)** | Injection simultanée de 6 pannes (MoMo, Orange, ENEO, Camwater, Canal+, S3P). | 6 diagnostics indépendants créés, tableau de bord réactif, 6 alertes délivrées sans blocage. | ✅ Conforme |
| **Test 6 : Registre 12 Col.** | Génération de l'audit mensuel de disponibilité. | Fichier Excel (.xlsx) produit avec formules SLA exactes et empreinte SHA-256. | ✅ Conforme |

#### 3. Résultats de Disponibilité Constatés (Exemple d'Audit)
* **Orange Money Cameroun** : Uptime **99.94 %** (26 min d'arrêt) ➔ *SLA Respecté*.
* **MTN Mobile Money** : Uptime **99.89 %** (48 min d'arrêt) ➔ *SLA En Dérogation*.
* **ENEO Prépayé** : Uptime **99.97 %** (12 min d'arrêt) ➔ *SLA Respecté*.

---

### 📂 DOSSIER 7 : GUIDES D'INSTALLATION & D'UTILISATION

#### 1. Guide d'Installation (Procédures Techniques)
1. Prérequis : PHP >= 8.3, Composer 2.x, PostgreSQL 16, Node.js 20+, Git.
2. Cloner le référentiel : `git clone https://github.com/vigilcore/vigilcore.git`
3. Installer les dépendances : `composer install && npm install && npm run build`
4. Paramétrer `.env` : Configurer `DB_CONNECTION=pgsql`, `N8N_WEBHOOK_URL`, `WHATSAPP_TOKEN`.
5. Migrations & Données de test : `php artisan migrate --seed`
6. Démarrage du planificateur de sondes : `php artisan schedule:work`

#### 2. Guide d'Utilisation
* **Pour l'Opérateur Support** : Surveillance des voyants sur le Dashboard, consultation du dossier d'incident et lecture du rapport de boîte noire.
* **Pour le Responsable SLA** : Sélection de la période mensuelle, filtrage par partenaire et clic sur *"Générer Registre d'Audit 12 Colonnes (.xlsx)"*.
* **Pour l'Administrateur** : Ajout d'une nouvelle passerelle partenaire (URL, méthode HTTP, seuil timeout).

---

## 📑 5. STRUCTURE COMPLÈTE & TABLE DES MATIÈRES TYPE (CANEVAS IAI)

Voici la structure exacte page par page à respecter pour le rapport final :

1. **Page de Garde Officielle** *(IAI / MAVIANCE / Double bordure / Ruban 2025-2026)*
2. **Dédicace** *(Page II — Bulle ronde bleue)*
3. **Remerciements** *(Page III — Liste structurée personnalisée)*
4. **Sommaire** *(Page IV — Lignes pointillées sans tableau)*
5. **Liste des Tableaux** *(Page V — Numérotation Tableau 1 à 33)*
6. **Liste des Figures** *(Page VI-VII — Figure 1 à 26)*
7. **Sigles et Abréviations** *(Page VIII — 2 colonnes)*
8. **Glossaire** *(Page IX à XI — Définitions techniques et métier)*
9. **Résumé (Français)** *(Page XII — Mots-clés)*
10. **Abstract (Anglais)** *(Page XIII — Keywords)*
11. **Introduction Générale** *(Page 1)*
12. **1ère PARTIE : PHASE D’INSERTION** *(Page 2 à 13)*
    * *Intercalaire avec Résumé & Aperçu*
    * *I. Accueil et intégration en entreprise*
    * *II. Présentation de l'entreprise MAVIANCE PLC (Historique, Plan Bonanjo, Organigramme, Missions, Activités, Ressources matérielles/logicielles, Partenaires)*
13. **2ème PARTIE : PHASE TECHNIQUE (Les 7 Dossiers)** *(Page 14 à 115)*
    * *Intercalaire 2ème Partie*
    * *Dossier 1 : L'Existant (Critique, Problématique, Solution VigilCore)*
    * *Dossier 2 : Cahier des charges (Besoins F/NF, Diagramme de Gantt 13 semaines, Budget FCFA, Livrables)*
    * *Dossier 3 : Dossier d'Analyse (Comparatif UML/MERISE, 2TUP, Use Cases Global & Spécifiques, Fiches textuelles IAI, Séquences, Activités)*
    * *Dossier 4 : Dossier de Conception (Classes , États-transitions, Paquetages)*
    * *Dossier 5 : Dossier de Réalisation (Stack Laravel 12 / Livewire / n8n / PostgreSQL, Architectures, Déploiement, Composants)*
    * *Dossier 6 : Tests de fonctionnalités (Sondes 30s, Boîte noire, Alertes WhatsApp, Métriques SLA)*
    * *Dossier 7 : Guide d'installation et d'utilisation (Procédures, Guides opérateurs, Dépannage)*
14. **Conclusion Générale & Perspectives** *(Page 116 - 117)*
15. **Annexes** *(Page 118)*
16. **Bibliographie et Webographie** *(Page 119 — Norme académique)*
17. **Lettre d'Admission en Stage & Fiches de Stage** *(Page 120 - 121)*
18. **Table des Matières Détaillée** *(Page 122+)*

---

### 💡 Consigne de Rédaction à transmettre à Claude :
> *"Tu es chargé de rédiger l'intégralité du rapport de stage de DASSI NZALI CALEB DAONY en respectant fidèlement le document de référence ci-dessus. Utilise un style académique soutenu, rigoureux et professionnel conforme à la charte de l'IAI-Cameroun (police Times New Roman, titres des tableaux au-dessus, titres des figures en-dessous, intercalaires avec Résumé & Aperçu, et cadrage clair sur la solution logicielle VigilCore développée pour la soutenance).*
