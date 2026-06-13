# ARTE21 – Cartes de membre

Application web PHP/MySQL pour la création et la gestion des cartes de membre de l'ASBL ARTE21.

---

## Fonctionnalités

- **Connexion sécurisée** (identifiant + mot de passe, protection CSRF, sessions sécurisées)
- **Création de cartes de membre** avec :
  - Nom, prénom, date de naissance
  - Date d'inscription, durée de validité (1–10 ans)
  - Date de validité **calculée automatiquement**
  - Photo du membre (JPEG/PNG, max 2 Mo)
  - Numéro de série unique au format `ARTE21/AAAA/NNNN`
- **Tableau de bord** listant toutes les cartes (sans les photos)
- **Vue détaillée + impression** de chaque carte (format carte de crédit ISO)

---

## Prérequis

- Hébergement PHP ≥ 7.4 (compatible OVH)
- Base de données MySQL / MariaDB
- Extension PHP : `pdo_mysql`, `fileinfo`
- Module Apache : `mod_rewrite`, `mod_headers` (recommandé)

---

## Installation

### 1. Déposer les fichiers

Téléversez tous les fichiers sur votre hébergement OVH via FTP/SFTP.

### 2. Créer la base de données

Dans votre espace OVH, créez une base de données MySQL.  
Importez le fichier `sql/schema.sql` via phpMyAdmin ou la CLI :

```bash
mysql -u votre_user -p votre_base < sql/schema.sql
```

### 3. Configurer la connexion

Éditez `config/database.php` et renseignez vos informations OVH :

```php
define('DB_HOST',   'sql123.cluster.ovh.net'); // Serveur MySQL OVH
define('DB_NAME',   'votre_base');
define('DB_USER',   'votre_user');
define('DB_PASS',   'votre_mot_de_passe');
```

### 4. Changer le mot de passe administrateur

Le compte par défaut est :
- **Identifiant** : `admin`
- **Mot de passe** : `ChangeMe2024!`

⚠️ **Changez-le immédiatement** en exécutant dans phpMyAdmin :

```sql
UPDATE users
SET password = '$2y$12$NOUVEAU_HASH'
WHERE username = 'admin';
```

Pour générer un hash bcrypt, utilisez ce script PHP en ligne de commande ou sur le serveur :

```php
<?php echo password_hash('VotreNouveauMotDePasse', PASSWORD_BCRYPT); ?>
```

### 5. Droits sur le dossier uploads

Le dossier `uploads/` doit être accessible en écriture par le serveur web :

```bash
chmod 750 uploads/
```

---

## Structure des fichiers

```
├── index.php          → Redirection (login ou tableau de bord)
├── login.php          → Page de connexion
├── logout.php         → Déconnexion
├── dashboard.php      → Liste des cartes
├── create_card.php    → Formulaire de création
├── card_detail.php    → Vue + impression d'une carte
├── config/
│   └── database.php   → Configuration BDD (à adapter)
├── includes/
│   ├── auth.php       → Authentification, CSRF, session
│   ├── functions.php  → Fonctions utilitaires
│   ├── header.php     → En-tête HTML
│   ├── footer.php     → Pied de page HTML
│   └── card_template.php → Template visuel de la carte
├── assets/
│   ├── css/style.css  → Styles principaux
│   └── js/app.js      → JavaScript (prévisualisation)
├── sql/
│   └── schema.sql     → Schéma de base de données
├── uploads/           → Photos (non versionnées)
└── .htaccess          → Règles de sécurité Apache
```

---

## Sécurité

- Mots de passe hashés avec **bcrypt** (PASSWORD_BCRYPT)
- **Tokens CSRF** sur tous les formulaires POST
- **Requêtes préparées PDO** (protection injection SQL)
- Vérification du type MIME réel des photos uploadées
- En-têtes HTTP de sécurité (X-Frame-Options, CSP, etc.)
- Accès direct aux dossiers `config/`, `includes/`, `sql/` bloqué via `.htaccess`
- Exécution de PHP dans `uploads/` interdite

---

## Format de référence

Chaque carte reçoit une référence unique au format :

```
ARTE21 / année d'inscription / numéro séquentiel (sur 4 chiffres)
```

**Exemple** : `ARTE21/2024/0001`, `ARTE21/2024/0002`, `ARTE21/2025/0001`

Le numéro repart à 1 chaque année civile.
