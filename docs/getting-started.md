# Getting Started with Panulat

Panulat is a modular, lightweight PHP framework for building clean REST APIs and API-first applications.

This guide walks you through creating a new Panulat API project, running it locally, understanding the project structure, adding routes and controllers, working with the database, testing your API, and preparing it for deployment.

## Requirements

Before you start, make sure you have:

* PHP 8.3 or higher
* Composer 2
* PDO extension
* JSON extension
* A database driver such as `pdo_mysql` or `pdo_sqlite`

Optional:

* Docker and Docker Compose

## Create a New Project

Create a new Panulat application with Composer:

```bash
composer create-project blcklab/panulat my-api
```

Go into the project directory:

```bash
cd my-api
```


## Running with Docker

Panulat includes a local Docker setup for development.

Start the app and database:

```bash
docker compose up --build
```

The API will be available at:

```txt
http://127.0.0.1:8080
```

Test it with:

```bash
curl http://127.0.0.1:8080/v1/health
```

Stop the Docker containers:

```bash
docker compose down
```

Open a shell inside the app container:

```bash
docker compose exec app sh
```

Run migrations inside Docker:

```bash
docker compose exec app php bin/console migrate
```

Run seeders:

```bash
docker compose exec app php bin/console db:seed
```

## Local Machine

Run the setup command:

```bash
composer setup
```

Start the local development server:

```bash
composer serve
```

Your API should now be available at:

```txt
http://127.0.0.1:8000
```

Test the health endpoint:

```bash
curl http://127.0.0.1:8000/v1/health
```

Expected response:

```json
{
  "status": "ok"
}
```



## Project Structure

A Panulat starter project looks like this:

```txt
app/
├── Controllers/
├── Middleware/
├── Models/
├── Providers/
└── Resources/

config/
├── app.php
├── database.php
├── cache.php
├── cors.php
├── jwt.php
└── logging.php

database/
├── migrations/
└── seeders/

public/
└── index.php

routes/
└── api.php

storage/
├── cache/
└── logs/

tests/
bin/console
black
composer.json
```

Important folders:

* `app/Controllers` contains your API controllers.
* `routes/api.php` contains your API routes.
* `config/` contains application configuration.
* `database/migrations` contains database table migrations.
* `database/seeders` contains seed data.
* `storage/` contains cache files and logs.
* `public/index.php` is the front controller for HTTP requests.

## Environment Configuration

Panulat uses a `.env` file for local environment values.

If `.env` does not exist yet, copy it from `.env.example`:

```bash
cp .env.example .env
```

Important values:

```env
APP_NAME="Panulat API"
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

JWT_SECRET=change-this-local-secret
```

For production, use:

```env
APP_ENV=production
APP_DEBUG=false
```

Never commit production secrets.

## Routes

Routes live in:

```txt
routes/api.php
```

Example route:

```php
use Panulat\Routing\Router;
use Panulat\Http\Response;

return function (Router $router): void {
    $router->get('/v1/hello', function () {
        return Response::json([
            'message' => 'Hello from Panulat',
        ]);
    });
};
```

Test it:

```bash
curl http://127.0.0.1:8000/v1/hello
```

Expected response:

```json
{
  "message": "Hello from Panulat"
}
```

## RESTful Routes

A typical REST API resource uses routes like these:

```php
$router->get('/v1/users', [UserController::class, 'index']);
$router->post('/v1/users', [UserController::class, 'store']);
$router->get('/v1/users/{id}', [UserController::class, 'show']);
$router->put('/v1/users/{id}', [UserController::class, 'update']);
$router->delete('/v1/users/{id}', [UserController::class, 'destroy']);
```

Common REST meaning:

```txt
GET    /v1/users       List users
POST   /v1/users       Create user
GET    /v1/users/{id}  Show one user
PUT    /v1/users/{id}  Update user
DELETE /v1/users/{id}  Delete user
```

## Creating a Controller

Panulat CLI provides scaffolding commands through `blcklab/panulat-cli`.

Create a controller:

```bash
php black make:controller UserController
```

This creates a file like:

```txt
app/Controllers/UserController.php
```

Example controller:

```php
<?php

declare(strict_types=1);

namespace App\Controllers;

use Panulat\Http\Request;
use Panulat\Http\Response;

final class UserController
{
    public function index(Request $request): Response
    {
        return Response::json([
            'data' => [
                [
                    'id' => 1,
                    'name' => 'Avelino',
                    'email' => 'avelino@example.test',
                ],
            ],
        ]);
    }

    public function show(Request $request, int|string $id): Response
    {
        return Response::json([
            'data' => [
                'id' => (int) $id,
                'name' => 'Avelino',
                'email' => 'avelino@example.test',
            ],
        ]);
    }
}
```

Register the routes:

```php
use App\Controllers\UserController;

$router->get('/v1/users', [UserController::class, 'index']);
$router->get('/v1/users/{id}', [UserController::class, 'show']);
```

## Requests

Panulat request objects include helpers for common API input.

```php
$name = $request->input('name');
$email = $request->input('email');
$page = $request->query('page', 1);
$data = $request->json();
```

Useful request methods:

```php
$request->json();          // JSON request body
$request->body();          // raw request body
$request->getParsedBody(); // form body
$request->post('name');    // form value
$request->query('page');   // query value
$request->input('name');   // JSON, form, then query fallback
```

## Responses

Return JSON:

```php
return Response::json([
    'message' => 'User created',
], 201);
```

Return plain text:

```php
return Response::text('ok');
```

Return no content:

```php
return Response::noContent();
```

Common status codes:

```txt
200 OK
201 Created
204 No Content
400 Bad Request
401 Unauthorized
403 Forbidden
404 Not Found
422 Validation Error
429 Too Many Requests
500 Server Error
```

## Middleware

Middleware runs before or after your controller.

Example route with middleware:

```php
$router->get('/v1/me', [MeController::class, 'show'], [
    'auth',
]);
```

Middleware can also be grouped:

```php
$router->group('/v1', function (Router $router): void {
    $router->get('/users', [UserController::class, 'index']);
}, ['api']);
```

Common middleware examples:

```txt
api
auth
jwt
api-key
throttle:api
throttle:login
```

## JWT Authentication

The starter includes `blcklab/panulat-jwt`.

Protected route example:

```php
$router->get('/v1/me', [MeController::class, 'show'], [
    'auth',
]);
```

Login route example:

```php
$router->post('/v1/auth/login', [AuthController::class, 'login'], [
    'throttle:login',
]);
```

Example login request:

```bash
curl -X POST http://127.0.0.1:8000/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.test","password":"password"}'
```

Example authenticated request:

```bash
curl http://127.0.0.1:8000/v1/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

Make sure your `.env` has a strong `JWT_SECRET`:

```env
JWT_SECRET=change-this-to-a-long-random-secret
```

For production, never use the default local secret.

## Database

Panulat Core includes a PDO database layer and query builder.

Example query:

```php
$users = $db->table('users')
    ->select(['id', 'name', 'email'])
    ->whereNull('deleted_at')
    ->orderBy('id', 'desc')
    ->paginate(page: 1, perPage: 20);
```

Insert a record:

```php
$userId = $db->table('users')->insertGetId([
    'name' => 'Avelino',
    'email' => 'avelino@example.test',
]);
```

Update a record:

```php
$db->table('users')
    ->where('id', '=', $id)
    ->update([
        'name' => 'Updated Name',
    ]);
```

Delete a record:

```php
$db->table('users')
    ->where('id', '=', $id)
    ->delete();
```

## Migrations

Create a migration:

```bash
php black make:migration create_users_table
```

Run migrations:

```bash
php bin/console migrate
```

Or through `black`:

```bash
php black migrate
```

With Docker:

```bash
docker compose exec app php black migrate
```

## Seeders

Create a seeder:

```bash
php black make:seeder UserSeeder
```

Run seeders:

```bash
php bin/console db:seed
```

Or through `black`:

```bash
php black db:seed
```

With Docker:

```bash
docker compose exec app php black db:seed
```

## Resources

Resources help you shape API responses.

Example:

```php
return Response::json([
    'data' => [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
    ],
]);
```

Recommended API response format:

```json
{
  "data": {
    "id": 1,
    "name": "Avelino",
    "email": "avelino@example.test"
  }
}
```

For lists:

```json
{
  "data": [],
  "meta": {
    "total": 10,
    "page": 1,
    "per_page": 20
  }
}
```

## Validation

Validate input before writing it to the database.

Example idea:

```php
$data = $request->json();

$validator->validate($data, [
    'name' => 'required',
    'email' => 'required|email',
]);
```

Validation errors should return `422 Unprocessable Entity`.

Example response:

```json
{
  "message": "Validation failed.",
  "errors": {
    "email": [
      "The email field is required."
    ]
  }
}
```

## CLI Commands

Panulat CLI uses the `black` command.

Create files:

```bash
php black make:controller UserController
php black make:middleware AuthMiddleware
php black make:model User
php black make:resource UserResource
php black make:migration create_users_table
php black make:seeder UserSeeder
```

Run framework commands:

```bash
php black migrate
php black db:seed
php black optimize
```

## Testing

Run tests:

```bash
composer test
```

Run static analysis:

```bash
composer stan
```

Run both:

```bash
composer check
```

With Docker:

```bash
docker compose exec app composer check
```

Test API endpoints:

```bash
composer test:api
```

With Docker:

```bash
composer docker:test-api
```

## Local Development Flow

Recommended non-Docker flow:

```bash
composer create-project blcklab/panulat my-api
cd my-api
composer setup
composer serve
```

Recommended Docker flow:

```bash
composer create-project blcklab/panulat my-api
cd my-api
docker compose up --build
```

Then open:

```txt
http://127.0.0.1:8080
```

## Production Deployment

For production:

```bash
composer install --no-dev --optimize-autoloader
php bin/console migrate
php bin/console optimize
```

Set production environment values:

```env
APP_ENV=production
APP_DEBUG=false
JWT_SECRET=your-real-long-random-secret
```

Point your web server to:

```txt
public/
```

Do not expose the project root publicly.

Production checklist:

```txt
APP_ENV=production
APP_DEBUG=false
Strong JWT_SECRET
Real database credentials
Storage directory is writable
Logs are configured
Web server points to public/
Dependencies installed with --no-dev
```

## Docker in Production

The included `docker-compose.yml` is intended for local development.

For production, use the deployment setup that fits your application, such as:

* PHP-FPM with Nginx
* Apache with PHP
* A Docker image on a server or platform
* A container orchestration platform
* Another PHP runtime setup

Do not commit production secrets.

## Package Architecture

Panulat is split into packages:

```txt
blcklab/panulat-core
    Framework foundation

blcklab/panulat-jwt
    Optional JWT authentication

blcklab/panulat-cli
    Optional developer CLI

blcklab/panulat
    Starter API project
```

The core stays small. Optional features are added through separate packages.

Future optional packages may include:

```txt
blcklab/panulat-redis
blcklab/panulat-queue
blcklab/panulat-testing
blcklab/panulat-openapi
```

## Next Steps

After finishing this guide, try creating a new controller:

```bash
php black make:controller ProductController
```

Then add routes:

```php
$router->get('/v1/products', [ProductController::class, 'index']);
$router->post('/v1/products', [ProductController::class, 'store']);
```

Test the endpoint:

```bash
curl http://127.0.0.1:8000/v1/products
```

You now have the basics needed to start building APIs with Panulat.
