# Sell your car 


This project contains all API of sell your car application


## Install

Do `git clone` of the project:

```bash
git clone
```

Run composer install:

```bash
composer install
```

Generate Keys pair for JWT tokens:

```bash
bin/console lexik:jwt:generate-keypair
```

Create database:

```bash
bin/console doctrine:database:create
```

Run migrations:

```bash
bin/console doctrine:migrations:migrate
```

## Env variables

<h3><b>Don't change .env file if not needed!</b></h3>

Create .env.local file and update it with local parameters if needed (ex: MAILER_DSN, APP_ENV, JWT_PASSPHRASE, DATABASE_URL).
<br/><br/>
<b>Variables from .env.local file will override .env file variables</b>


## Run server

Run local dev server using command bellow:

```bash
php -S localhost:3000 -t public/
```


## Create admin user

Create admin user using command bellow:

```bash
bin/console app:create-admin <email> <name> <password>
```

## Run tests

Run tests using command bellow:

```bash
bin/run-tests
```

