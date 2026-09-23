# Scripts d'Exploitation & d'Administration — VigilCore

Ce dossier regroupe les scripts d'administration système, de maintenance et d'interfaçage technique de la plateforme **VigilCore**.

---

### 1. `connexion_whatsapp.sh`
* **Rôle** : Assistant interactif CLI pour l'appairage rapide de l'instance WhatsApp (Evolution API / Baileys).
* **Fonctionnalités** :
  - Génération de code d'appairage par numéro de téléphone (sans scan caméra).
  - Affichage direct du QR code ASCII dans le terminal.
  - Réinitialisation et purge d'une session bloquée.

---

### 2. `reinitialisation_qr.sh`
* **Rôle** : Réinitialisation à chaud de l'instance d'authentification WhatsApp et affichage du flux de synchronisation QR.

---

### 3. `injection_logs_elasticsearch.sh`
* **Rôle** : Script d'injection et de synchronisation des métriques de télémétrie nominale des 20 services partenaires dans **Elasticsearch** (visualisables sous Kibana).

---

### 4. `generation_logos.cjs`
* **Rôle** : Utilitaire Node.js de conversion et de rendu haute résolution des logos vectoriels SVG vers les formats PNG transparents pour l'interface et les rapports d'audit.
