# VIGILCORE — Observabilité, Détection Proactive & Résilience Opérationnelle

> **Projet de Fin d'Études & Stage Professionnel**  
> **Institution :** Institut Africain d'Informatique (IAI-Cameroun) — Centre d'Excellence Technologique Paul BIYA  
> **Entreprise d'accueil :** Maviance PLC (Douala, Cameroun) • Écosystème Smobilpay  
> **Déploiement Production (VPS) :** [vigilcore.calebdevs.com](https://vigilcore.calebdevs.com)  

---

## 📌 Présentation du Projet

**VigilCore** est une plateforme intégrée de supervision proactive, de gestion des incidents et d'observabilité de haute disponibilité conçue pour sécuriser l'écosystème de paiement multi-services de **Maviance**.

Face aux pannes imprévues et aux dégradations de service des passerelles tierces (opérateurs télécoms, facturiers publics, distributeurs d'énergie), VigilCore apporte :
1. **Une détection proactive par sondes synthétiques** avec contrôle de santé à intervalles réguliers (toutes les 30 secondes).
2. **Une boîte noire forensique** scellée cryptographiquement par **SHA-256** garantissant l'inaltérabilité des preuves d'indisponibilité (SLA contractuels).
3. **Un moteur d'orchestration multicanal (< 5s)** s'appuyant sur des webhooks sécurisés, **n8n**, une passerelle **WhatsApp NOC** et une page de statut publique.
4. **Une console d'audit et de reporting** pour l'analyse décisionnelle et l'exportation des historiques d'incidents (PDF et Excel).

---

## 🏗️ Architecture Technique Globale

L'infrastructure s'articule autour d'une architecture modulaire et distribuée :

| Couche | Technologies Employées | Rôle Principal |
| :--- | :--- | :--- |
| **Backend Applicatif** | **PHP 8.3 / Laravel 11** | Cœur décisionnel, API REST, gestion des incidents et logique métier |
| **Interface Réactive** | **Livewire 3 / Tailwind CSS** | Tableau de bord temps réel, indicateurs de santé, filtres dynamiques |
| **Observabilité & Télémétrie** | **Elasticsearch / Kibana** | Centralisation des journaux d'événements, métriques de latence HTTP |
| **Orchestration & Alertes** | **n8n / Webhooks SSL** | Routage événementiel, déduplication et automatisation des alertes |
| **Canal d'Urgence NOC** | **Evolution API (WhatsApp)** | Notification push instantanée des équipes d'astreinte et gestionnaires |
| **Base de Données & Cache** | **MySQL 8 / Redis** | Persistance relationnelle des incidents et mise en cache haute performance |

---

## 📂 Structure & Organisation du Projet

Le projet respecte scrupuleusement les conventions d'architecture industrielle et les standards du framework Laravel :

```text
vigilcore/
├── app/                        # Cœur métier et logique applicative
│   ├── Console/Commands/       # Commandes CLI Artisan d'administration
│   ├── Http/Controllers/       # Contrôleurs Web et API REST (Webhooks, Exports)
│   ├── Livewire/               # Composants réactifs (Dashboard, Rapports)
│   ├── Models/                 # Modèles de données Eloquent (Incident, User)
│   └── Services/               # Logique de détection, FSM et intégrations
├── config/                     # Fichiers de configuration de l'application
├── database/                   # Migrations de schéma, seeders et structures relationnelles
├── diagrams/                   # Dossier de conception et modélisation UML / Architecture
├── public/                     # Point d'entrée web (index.php), assets compilés et logos
├── resources/                  # Vues Blade, templates d'emails et composants UI
├── routes/                     # Définition des routes applicatives (web.php, api.php)
├── scripts/                    # Scripts d'exploitation système et outils d'administration
├── storage/                    # Logs d'exécution, sessions et fichiers temporaires
├── tests/                      # Suites de tests automatisés (Feature et Unit)
└── demo.sh                     # Orchestrateur de simulation pour la soutenance (Exécuté sur VPS)
```

---

## 🛠️ Contenu du Répertoire `scripts/`

Pour faciliter l'administration du serveur et l'interfaçage avec les services conteneurisés du VPS, des utilitaires dédiés sont regroupés dans `scripts/` :

* **`connexion_whatsapp.sh`** : Assistant d'appairage direct de l'instance WhatsApp (via code d'association numérique ou QR code).
* **`reinitialisation_qr.sh`** : Procédure de purge et de réinitialisation à chaud du conteneur d'alerting WhatsApp.
* **`injection_logs_elasticsearch.sh`** : Synchronisation des métriques nominales des 20 services partenaires dans Elasticsearch.
* **`generation_logos.cjs`** : Utilitaire de rendu des logos vectoriels officiels des partenaires intégrateurs.

---

## 🚀 Protocole de Démonstration (Soutenance)

La plateforme de production et l'ensemble de ses briques (Laravel, MySQL, Redis, n8n, Elasticsearch, Kibana, WhatsApp Gateway) sont **déployées en ligne sur le VPS**.

Lors de la soutenance :
1. **Visualisation en direct :** Ouvrez l'interface web sur [https://vigilcore.calebdevs.com](https://vigilcore.calebdevs.com).
2. **Simulation d'incidents :** Connectez-vous en SSH au VPS et lancez l'orchestrateur :
   ```bash
   ./demo.sh
   ```
3. **Scénarios disponibles :**
   * Choix d'une panne unitaire parmi les 20 passerelles (ex: Orange Money, MTN MoMo, Eneo, Camwater, Canal+).
   * **Pack 15 services majeurs** pour démontrer la gestion d'incidents massifs.
   * **Blackout global (20/20)** démontrant la résilience de la machine d'états (FSM) et le déclenchement des coupe-circuits.
   * **Rétablissement complet (touche `r`)** pour clôturer tous les incidents et ramener les 20 passerelles à l'état opérationnel.

---

## 🔒 Sécurité & Intégrité

* **Scellement SHA-256** : Chaque incident enregistré génère une signature cryptographique immuable combinant le service, le code de statut HTTP, le motif d'erreur, le serveur émetteur et l'horodatage UTC.
* **Protection des accès** : Authentification stricte sur le tableau de bord avec contrôle des rôles et sessions sécurisées.
