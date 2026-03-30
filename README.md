# Car Express API

Backend Laravel 13 pour le frontend React `ProjetCarExpress/carexpress-react`.

## Architecture

Le projet suit maintenant une organisation inspirée d'une architecture professionnelle en couches :

- `app/Http/Controllers/Api`
- `app/Http/Requests`
- `app/Http/Resources`
- `app/Console`
- `app/Exceptions`
- `app/Jobs`
- `app/Repository`
- `app/Services`
- `app/Traits`
- `app/Utils`
- `docs`

Le détail est documenté dans [`docs/ARCHITECTURE.md`](./docs/ARCHITECTURE.md).

## Fichiers cles

| Fichier | Role |
|---------|------|
| `routes/api.php` | Definition des endpoints |
| `app/Http/Controllers/Api/*` | Controleurs (validation + appel service) |
| `app/Services/*` | Logique metier |
| `app/Repository/*` | Acces aux donnees |
| `app/Models/*` | Modeles ORM Eloquent |
| `database/migrations/*` | Schema DB |
| `docs/swagger.json` | Documentation API (OpenAPI) |
| `.env.example` | Exemple configuration locale / Railway / Render |
| `Dockerfile` | Image Docker app |
| `docker-compose.yml` | Orchestration locale |
| `render.yaml` | Manifest deploiement Render |

## Stack

- Laravel 13
- PostgreSQL
- Laravel Sanctum
- L5 Swagger / OpenAPI

## Modules

- Authentification par rôles: `client`, `agency`, `admin`
- Catalogue véhicules location / vente
- Réservations location
- Demandes d'achat avec frais de service
- Dashboard agence
- Supervision admin
- Swagger UI sur `/api/documentation`

## Installation

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan l5-swagger:generate
php artisan serve
```

## Docker

```bash
cp .env.example .env
docker compose up --build
```

## PostgreSQL

Variables à renseigner dans `.env` :

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=carexpress
DB_USERNAME=postgres
DB_PASSWORD=postgres
L5_SWAGGER_CONST_HOST=http://localhost:8000
```

## Comptes de démonstration

- Admin: `admin@carexpress.sn` / `admin12345`
- Client: `client@carexpress.sn` / `client12345`
- Agence: `agency+dakar-auto-services@carexpress.sn` / `agency12345`

## Base API

Toutes les routes sont préfixées par `/api/v1`.

## Format de reponse API

Succes :

```json
{
  "status": true,
  "message": "Texte informatif",
  "data": {}
}
```

Erreur :

```json
{
  "status": false,
  "message": "Texte d'erreur",
  "errors": {
    "field": [
      "message de validation"
    ]
  }
}
```
