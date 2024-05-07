# JWT Practices

A Laravel application with developed REST APIs using JWT for authentication and authorization.

## Technical Stack
- PHP >= 8.1
- Laravel 10

## Project Setup
Clone the project source code from the [Repository](https://github.com/abdulmujeebcs/laravel-jwt).
```bash
git clone https://github.com/abdulmujeebcs/laravel-jwt
```

```bash
composer install
```

## Create Environments/Global configuration file 
Update your `.env` file Information before serving the application

```bash
cp .env.example .env
```

## JWT 
Run

```bash
php artisan jwt:secret
```
Key genration for application

```bash
php artisan key:generate
```

```bash
php artisan optimize
```

## Run Migrations

```bash
php artisan migrate
```


## Contributing
Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

Please make sure to update tests as appropriate.