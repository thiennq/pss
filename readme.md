# EYE Framework

## Requirement
- PHP 5.6+
- PHP Packages: php-cli, php-mysql, php-curl, php-fpm, php-mbstring, php-gd, php-memcached, php-xml, php-mcrypt
- MySQL
- composer, git
- Node.js (optional - for compiling SASS & Gulp build)
- Memcached (optional)

## How To Install
```bash
composer install
cp .env.example .env
# edit .env file for db config, template engine, 3rd-party services config
cd db
php create_table.php
```
