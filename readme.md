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

## Run for development
```bash
cd public
php -S localhost:9000
```

## How to deploy
Just upload all folders/files into `www` or `htdocs` folder.

Don't forgot to exclude `node_modules`, `.git` folder.

Enable cache (CACHE=cache) in .env for better performance.
