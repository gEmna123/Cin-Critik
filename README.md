# 🎬 Cin-Critik

> Plateforme web de critiques culturelles développée en **PHP** et **PostgreSQL** permettant la gestion des œuvres, auteurs, événements et critiques utilisateurs.

---

# ✨ Fonctionnalités

* 🔐 Authentification des utilisateurs (connexion, inscription, déconnexion)
* 🎨 Gestion des œuvres et des auteurs
* 📅 Consultation des événements culturels
* ⭐ Publication et suppression de critiques
* 👤 Gestion du compte utilisateur
* 🛠️ Interface d'administration
* 🔑 Réinitialisation de mot de passe
* 🛡️ Utilisation de PDO et de requêtes préparées pour sécuriser les accès à la base de données

---

# 🚀 Installation

## 1️⃣ Cloner le projet

```bash
git clone <repository_url>
cd Cin-Critik
```

## 2️⃣ Configurer la base de données

Créer une base PostgreSQL puis exécuter :

```bash
psql -U postgres -d cin_critik -f admin/test_bd.sql
```

## 3️⃣ Configurer la connexion

Modifier les informations de connexion dans :

```bash
database.php
```

Exemple :

```php
$host = "localhost";
$dbname = "cin_critik";
$user = "postgres";
$password = "password";
```

## 4️⃣ Lancer le serveur

```bash
php -S localhost:8000
```

Accéder ensuite à :

```text
http://localhost:8000
```

---

# 📂 Structure du projet

```text
.
├── 🏠 index.php                 # Page d'accueil
├── 🔐 login.php                 # Connexion
├── 📝 register.php              # Inscription
├── 🚪 logout.php                # Déconnexion
├── 👤 account.php               # Profil utilisateur
├── 📅 events.php                # Liste des événements
├── ⚙️ admin.php                 # Administration
├── 🗄️ database.php             # Connexion PDO
│
├── auteurs/
│   ├── 📖 liste_auteur.php      # Liste des auteurs
│   ├── 👨‍🎨 detail_auteur.php     # Détail d'un auteur
│   └── 🖼️ images/               # Images des auteurs
│
├── oeuvres/
│   ├── 🎭 liste.php             # Liste des œuvres
│   ├── 🎬 detail.php            # Détail d'une œuvre
│   └── 🖼️ images/               # Images des œuvres
│
├── includes/
│   ├── 🧭 navbar.php            # Barre de navigation
│   └── 📄 footer.php            # Pied de page
│
├── 🎨 public_css/               # Feuilles de style
│
└── admin/
    └── 🗃️ test_bd.sql           # Script de création de la base
```

---

# 🛠️ Technologies utilisées

* 🐘 PHP 8+
* 🐘 PostgreSQL
* 🔗 PDO
* 🌐 HTML5
* 🎨 CSS3
* 🔒 Sessions PHP

---

# 🔐 Sécurité

* ✅ Requêtes préparées PDO
* 🔑 Hachage des mots de passe avec `password_hash()`
* ✔️ Vérification avec `password_verify()`
* 🛡️ Protection contre les injections SQL
* 📋 Validation des données utilisateur

---

# 👥 Équipe

Projet réalisé dans le cadre d'un projet universitaire autour du développement web avec **PHP** et **PostgreSQL**.
