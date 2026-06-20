# 🎬 Cin-Critik

> Plateforme web collaborative de critiques culturelles développée en **PHP** et **PostgreSQL**, permettant la découverte, la gestion et l’évaluation d’œuvres culturelles à travers une expérience interactive.

---

## ✨ Fonctionnalités

- 📚 Gestion des œuvres culturelles
- ✍️ Gestion des auteurs
- ⭐ Publication et consultation de critiques
- 🎭 Gestion des événements culturels
- 👤 Gestion des utilisateurs
- 🔍 Recherche et consultation des contenus

---

## 🛠️ Technologies utilisées

- PHP
- PostgreSQL
- HTML5
- CSS3
- JavaScript

---

## 📂 Structure du projet

```text
Cin-Critik/
├── admin/
├── auteurs/
├── critiques/
├── evenement/
├── oeuvres/
├── utilisateurs/
├── index.php
└── README.md
```

---

## 🚀 Installation

### 1. Cloner le dépôt

```bash
git clone <url-du-repository>
cd Cin-Critik
```

### 2. Créer la base de données PostgreSQL

Créer une base de données :

```sql
CREATE DATABASE cin_critik;
```

### 3. Importer le schéma

```bash
psql -U postgres -d cin_critik -f admin/test_bd.sql
```

### 4. Configurer la connexion à la base de données

Modifier les paramètres de connexion PostgreSQL dans le fichier de configuration du projet.

---

## ▶️ Lancement du projet

Depuis la racine du projet :

```bash
php -S localhost:8000
```

Puis ouvrir dans votre navigateur :

```text
http://localhost:8000
```

---

## 👥 Équipe

Projet réalisé dans le cadre d'un projet académique.

---

## 📜 Licence

Projet à vocation pédagogique.
