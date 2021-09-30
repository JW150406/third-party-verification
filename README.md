- Take a git clone

- Install/Update composer by following command: <br/>
`composer install` or `composer update`

- Take original .env file and store within root directory of repo then follow below commands:

```
php artisan key:generate
php artisan config:cache
php artisan cache:clear
```

- To execute Migrations:

```
php artisan migrate --path=/database/migrations/new
```

- To setup storage:

```php artisan storage:link```

- Give read/write/view permissions to storage and public folder

- Seeders to setup permissions, roles, zipcodes and default admin user

```
php artisan config:cache
php artisan db:seed
```

- To authenticate APIs and generate api token follow below command

```
php artisan passport:install
```

Note: If modification done in `.env` or config folders file, make sure to execute following commands to reflect implemented changes

```
php artisan config:cache
php artisan cache:clear
```

