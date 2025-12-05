BlogCMS
Système de gestion de blog en PHP procédural avec MySQL.
Technologies

PHP 8 (Procédural)
MySQL / PostgreSQL
PDO avec requêtes préparées
HTML5 / CSS3 / Tailwind CSS
JavaScript

Structure du Projet
blogcms/
│
├── config/
│   └── database.php
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
│
├── admin/
│   ├── index.php
│   ├── categories.php
│   ├── comments.php
│   └── users.php
│
├── author/
│   ├── index.php
│   ├── articles.php
│   ├── create-article.php
│   └── edit-article.php
│
├── public/
│   ├── index.php
│   ├── article.php
│   └── search.php
│
├── auth/
│   ├── login.php
│   └── logout.php
│
├── uploads/
│   └── images/
│
├── assets/
│   ├── css/
│   └── js/
│
└── database/
    ├── schema.sql
    └── seeds.sql
Installation

Créer la base de données

bashmysql -u root -p
CREATE DATABASE blogcms;
USE blogcms;
SOURCE database/schema.sql;

Configurer config/database.php

phpdefine('DB_HOST', 'localhost');
define('DB_NAME', 'blogcms');
define('DB_USER', 'root');
define('DB_PASS', '');

Configurer permissions

bashchmod 755 uploads/images/
Comptes par défaut
Admin

Email: admin@blogcms.com
Password: Admin123!

Auteur

Email: auteur@blogcms.com
Password: Auteur123!

Fonctionnalités
Admin

Dashboard statistiques
CRUD catégories
Modération commentaires
Gestion utilisateurs

Auteur

Créer/Modifier/Supprimer articles
Poster commentaires

Visiteur

Voir articles
Poster commentaires

Bonus

Upload images
Recherche articles
Pagination

Sécurité

Requêtes préparées PDO
Hashage bcrypt
Protection XSS (htmlspecialchars)
Protection CSRF
Sessions sécurisées