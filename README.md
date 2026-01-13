# Database Manager (Mini-phpMyAdmin)

## 📌 Description du Projet
**Database Manager** est une interface web simplifiée, moderne et éducative pour la gestion de bases de données **MySQL/MariaDB**.  
Conçu comme une **alternative légère à phpMyAdmin**, ce projet met l’accent sur la clarté, la performance et une expérience utilisateur moderne.

Il permet de :
- se connecter à une base de données MySQL
- gérer les tables (CRUD)
- exécuter des requêtes SQL personnalisées
- visualiser des statistiques en temps réel

---

## 🛠️ Outils et Technologies

### Frontend
- **HTML5** : structure sémantique des pages
- **Vanilla CSS** : design système personnalisé  
  (Glassmorphism, animations fluides, responsive)
- **Vanilla JavaScript** :
  - Fetch API pour les requêtes asynchrones
  - Manipulation dynamique du DOM

### Backend
- **PHP** : traitement des requêtes côté serveur
- **MySQL / MariaDB** : système de gestion de base de données
- **Extension mysqli** : communication sécurisée et robuste avec la base de données

### Serveur local
- Scripts **Batch (.bat)** et **PowerShell (.ps1)** pour lancer rapidement un serveur PHP intégré

---
## 📂 Structure du Projet

```text
DEV_PROJET/
├── api/                  # Backend PHP (logique principale)
│   ├── db.php            # Connexion à la base de données & sessions
│   ├── login.php         # Authentification
│   ├── query.php         # Exécution des requêtes SQL
│   ├── stats.php         # Statistiques du dashboard
│   └── tables.php        # Gestion CRUD des tables
├── css/                  # Styles (Design System)
├── js/                   # Logique Frontend (app.js)
├── dashboard.html        # Dashboard & statistiques
├── index.html            # Page de connexion
├── sql.html              # Console SQL interactive
├── tables.html           # Explorateur des tables
└── run_local_server.bat  # Lancement rapide du serveur local
```


---

## 🚀 Comment accéder au projet

### Prérequis
- PHP installé
- MySQL ou MariaDB installé  
  *(via XAMPP, WAMP ou installation manuelle)*

### Lancement
1. Double-cliquez sur :
run_local_server.bat


2. Le serveur PHP local démarre sur le port **8000**

### Accès
Ouvrez votre navigateur et rendez-vous sur :
http://localhost:8000


---

## 💡 Utilisation du projet

### 🔐 Connexion
- Hôte : `localhost`
- Utilisateur : `root` (par défaut)
- Mot de passe : selon votre configuration
- Nom de la base de données

### 📊 Dashboard
- Nombre de tables
- Espace utilisé
- Statistiques générales en temps réel

### 🗃️ Gestion des Tables
- Liste des tables
- Visualisation des données
- Ajout, modification et suppression des enregistrements

### 🧪 Playground SQL
- Exécution de requêtes SQL :
  - **DDL** : `CREATE`, `DROP`
  - **DML** : `INSERT`, `UPDATE`, `DELETE`
  - **DQL** : `SELECT`
- Résultats affichés instantanément dans l’interface

---

## 🎯 Objectifs pédagogiques
- Comprendre l’architecture client / serveur
- Manipuler une base de données MySQL via PHP
- Créer une interface web moderne sans framework
- Mettre en pratique Git et GitHub

---

## 👤 Auteur
**Mohamed Lechhab**\n
**Mohamed Dradi**\n
**Nour-ddin Lali**
