# Guide d'exécution — Gestion des Événements

Application complète : back-end **Laravel 12** (API REST + Sanctum + MySQL + MongoDB) et front-end **Angular** (SPA standalone, Reactive Forms, Signals).

## 1. Prérequis

- PHP ^8.2, Composer
- Node.js ^22.22.3 (ou ^24.15 / ≥26) et npm
- MySQL (ou MariaDB) pour les données métier (utilisateurs, événements, salles, réservations, billets)
- MongoDB pour les données SI40 (logs d'activité, métadonnées de fichiers, statistiques d'usage)

## 2. Lancer le back-end (Laravel)

```bash
cd backend
composer install
cp .env.example .env        # si .env n'existe pas encore
php artisan key:generate

# Sanctum (authentification par token Bearer) — requis par AuthController/User::createToken()
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# DomPDF (génération du billet PDF joint à l'email de confirmation de réservation)
composer require barryvdh/laravel-dompdf
```

Dans `.env`, configurer les deux connexions :

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_evenements
DB_USERNAME=root
DB_PASSWORD=

MONGODB_DSN=mongodb://127.0.0.1:27017
MONGODB_DATABASE=gestion_evenements_logs
```

Puis :

```bash
php artisan migrate          # crée les tables MySQL (users, events, salles, reservations, tickets...)
php artisan db:seed          # (optionnel) données de démonstration, si un seeder est fourni
php artisan storage:link     # pour servir les fichiers uploadés (FileController)
php artisan serve            # démarre l'API sur http://localhost:8000
```

L'API est accessible sur `http://localhost:8000/api`. Vérification rapide :

```bash
curl http://localhost:8000/api/test
# {"message":"API Laravel OK"}
```

### Endpoints principaux

| Domaine | Routes |
|---|---|
| Auth (Sanctum) | `POST /api/auth/register`, `POST /api/auth/login`, `POST /api/auth/logout`, `GET /api/auth/me` |
| Événements | `GET /api/events`, `GET /api/events/{id}` (public) ; `POST/PUT/DELETE` (admin) |
| Salles | `GET /api/salles`, `GET /api/salles/{id}` (public) ; `POST/PUT/DELETE` (admin) |
| Réservations | `GET/POST/DELETE /api/reservations` (utilisateur connecté) |
| Billets | `GET /api/tickets`, `GET /api/tickets/{id}` (public) ; gestion CRUD (admin) |
| Fichiers | `GET/POST/DELETE /api/files` (upload, métadonnées stockées dans MongoDB) |
| Statistiques (SI40) | `GET /api/stats/overview`, `GET /api/stats/activity` |

L'accès aux routes de gestion (création/édition/suppression d'événements, salles, billets) est restreint aux comptes ayant le rôle `admin`, via le middleware `role:admin` (`App\Http\Middleware\EnsureUserHasRole`).

## 3. Lancer le front-end (Angular)

```bash
cd frontend
npm install
npm start            # ng serve, http://localhost:4200
```

Le fichier `src/environments/environment.ts` pointe déjà vers `http://localhost:8000/api`. Pour un build de production :

```bash
npm run build        # génère frontend/dist/frontend
```

### Structure de l'application

- **Architecture** : `core/` (services, guards, intercepteur HTTP, modèles), `shared/`, `features/` (un dossier par module métier : `auth`, `events`, `salles`, `reservations`, `tickets`, `dashboard`)
- **Routage** : routes dynamiques avec guards fonctionnels (`authGuard`, `roleGuard(['admin'])`) protégeant les pages de gestion et le tableau de bord
- **Formulaires** : Reactive Forms (`FormBuilder`, `Validators`) avec validations croisées (ex. confirmation de mot de passe à l'inscription, nombre de places ≤ places disponibles)
- **État** : Signals (`signal`/`computed`) pour le chargement, les erreurs, le tri et le filtrage côté client des listes (événements, salles, réservations, billets)
- **Communication API** : `HttpClient` + intercepteur ajoutant le token Sanctum, services typés par domaine consommés via RxJS (`switchMap` pour la résolution des paramètres de route)

## 4. Comptes de test

Après `php artisan db:seed` (ou via `php artisan tinker` / l'inscription `/register`), créer au moins :
- un compte **admin** (rôle `admin`) pour accéder à `/events/add`, `/salles/add`, `/tickets`, `/dashboard`
- un compte **utilisateur** standard pour réserver des places (`/events/{id}/reserve`, `/reservations`)

## 5. Intégration MongoDB (volet SI40)

Trois collections MongoDB complètent les données relationnelles MySQL :

- `ActivityLog` : journal d'activité (connexions, créations/modifications/suppressions) consultable via `GET /api/stats/activity`
- `FileMeta` : métadonnées des fichiers uploadés (nom, type, taille, propriétaire) liées aux pièces jointes gérées par `FileController`
- `StatEntry` : indicateurs d'usage agrégés (ex. compteurs par type de métrique), exploités par `StatController::overview()` via le pipeline d'agrégation MongoDB (`$group`, `$sort`)

Le tableau de bord Angular (`/dashboard`, réservé aux admins) consomme `GET /api/stats/overview`, qui croise les volumes MySQL (événements, réservations, places réservées, top événements) avec les métriques MongoDB.

### Connexion à un outil de reporting (Power BI ou équivalent)

`GET /api/stats/overview` et `GET /api/stats/activity` renvoient du JSON structuré directement exploitable par un connecteur HTTP/JSON dans un outil de reporting (Power BI, Metabase...). Pour une intégration directe à MongoDB, ces mêmes outils peuvent aussi se connecter nativement aux collections `activity_logs`, `file_meta` et `stat_entries` via un connecteur MongoDB (DSN `MONGODB_DSN` du `.env`).

## 6. Email de confirmation + billet PDF

À chaque création de réservation (`POST /api/reservations`), `ReservationController::store()` envoie
automatiquement au client un email de confirmation (`App\Mail\ReservationConfirmation`) contenant :

- un récapitulatif de la réservation (événement, date/heure, salle, nombre de places, référence) ;
- le **billet au format PDF** en pièce jointe, généré à la volée via DomPDF à partir de la vue
  `resources/views/tickets.pdf` (titre, date, salle, titulaire, référence `RES-000123`).

Pour que cela fonctionne, configurer l'envoi de mail dans `.env` — en développement, le plus simple est :

```
MAIL_MAILER=log
```

(les emails sont alors écrits dans `storage/logs/laravel.log` au lieu d'être réellement envoyés — pratique pour vérifier le contenu sans serveur SMTP). Pour un envoi réel, renseigner `MAIL_MAILER=smtp` et les identifiants du fournisseur (`MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`).

Une erreur d'envoi (SMTP mal configuré, etc.) est journalisée mais ne fait pas échouer la création de la réservation.

## 7. Vérifications effectuées

- **Front-end** : `ng build` compile et génère le bundle de production sans erreur TypeScript (`Application bundle generation complete`, sortie dans `frontend/dist/frontend`)
- **Back-end** : structure vérifiée (contrôleurs, modèles MySQL et Mongo, migrations, middleware de rôle, routes API) ; `vendor/` déjà installé. L'exécution de `php artisan migrate`/`serve`/tests nécessite un environnement avec PHP, MySQL et MongoDB disponibles (non présents dans cet environnement de vérification) — à exécuter localement avec les commandes de la section 2.
