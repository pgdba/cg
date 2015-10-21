cg
=========

A Symfony project created on October 21, 2015, 7:49 pm.

## Requirements
* PHP 5.4
* Relational database, MySQL preferred

## Installation

1. While in project directory run `php composer.phar install`

2. Provide database configuration

3. Run `php app/console doctrine:database:create` to create database configured in previous step

4. Run `php app/console doctrine:schema:create` to restore database structure

5. Create vhost or run php webserver (document root is in web directory)


