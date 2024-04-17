# Platform BMS

Platform is a multisite and multilingual compatibility Business Management System for growing any business. Free and open-source and always will be.

---

## How to install?

1. Clone source code from GitHub
2. Copy `.env.dist` to `.env` and modify.
3. Run `composer install` command
4. Create database with `php bin/console doctrine:database:create` command
5. Migrate database with `php bin/console doctrine:migrations:migrate` command
6. Update Composer dependencies with `composer update` command

## How to update?

One line command to update:

```shell
git status; git pull; php bin/console doctrine:migrations:migrate; composer update; composer dump-autoload -o; php bin/console cache:clear;
```

## Documentations

### Developer Book

Based on:
- latest Symfony PHP Framework (https://symfony.com)
- latest Twig template engine (https://twig.symfony.com/)
- latest Bootstrap (https://getbootstrap.com)
- latest chart.js (https://www.chartjs.org/)

Versions under Git History.

---

## Symfony commands

```shell
# create new Controller
symfony console make:controller NewController
# create new Entity
php bin/console make:entity
# create a migrations
php bin/console make:migration
# run migrations
php bin/console doctrine:migrations:migrate
```

---

## Copyright

Platform made with :green_heart: in Budapest by Harkály Gergő (https://www.harkalygergo.hu).
