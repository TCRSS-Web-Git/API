<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>


## About WaWa X Starter API

API สำหรับ website และ admin

## Framework & Tools used

- Laravel 11

# Development

### API Documents

Please visit /docs for API documents.

### Requirement

- PHP 8.3
- MySQL 8
- Redis

### Installation

1. Clone project (and initialize git flow if using SourceTree [main, develop])
2. Create .env file by copy content from .env.example `cp .env.example .env`
3. Config custom DNS for your machine ([api.starter.test](https://api.starter.test)), Make sure url
   match `APP_URL` in .env, also
   config `FRONTEND_URL` and `SESSION_DOMAIN` if you're using custom domain for frontend.
   See [Config Valet](development_docs/config_valet.md) for how to config Valet on Mac.
4. Run `composer install` (for Windows user,
   use `composer install --ignore-platform-req ext-pcntl --ignore-platform-req ext-posix`)
5. Run `php artisan key:generate`
6. Run `php artisan storage:link`
7. Create Database named **_api.starter_** (MySQL 8, Collation: utf8mb4_unicode_ci), update `DB_USERNAME`, `DB_PASSWORD` in .env
8. Config MySQL to support timezone [Detail](development_docs/config_mysql.md)
9. Make sure your .env QUEUE_CONNECTION is set to `redis`. Start queue work, run `php artisan queue:work --queue=default,low` (For local dev only, We use Laravel Horizon in
   test and production server). <a href="config_dev_queue_automatically.md" target="_blank">How to automatically run Laravel queue worker using PhpStorm</a>
10. Run `php artisan migrate --seed` to migrate and seed database
11. (_optional_) Config local email testing such as <a href="https://mailtrap.io/" target="_blank">Mailtrap</a>
    , <a href="https://github.com/axllent/mailpit" target="_blank">Mailpit</a>,
    or <a href="https://usehelo.com" target="_blank">HELO</a>
12. Create new feature branch to start working.


### Development Documents

For other development documents, please visit [development_docs](development_docs/README.md)
