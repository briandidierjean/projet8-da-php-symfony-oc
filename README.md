ToDo & Co
========

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/6de9261137d34ae0b819fe39d9812984)](https://app.codacy.com/gh/briandidierjean/projet8-da-php-symfony-oc?utm_source=github.com&utm_medium=referral&utm_content=briandidierjean/projet8-da-php-symfony-oc&utm_campaign=Badge_Grade_Settings)

## 1. Installation

### 1.1. Installation with Docker (recommended)

Here is the recommended installation with Docker that includes PHP, MySQL and phpMyAdmin to get a functional application in no
time.

-  Clone the repository.
-  Go to the project root.
-  Execute `docker-compose up` in your terminal.
-  Execute `php bin/console doctrine:schema:update --force` in www_docker container shell.
-  Execute `php bin/console doctrine:fixture:load` in www_docker container shell.
-  Create a *.env.local* file and add `APP_ENV=prod` in www_docker container shell.
-  Execute `rm -rf var/cache/` in www_docker container shell.

The application is ready ! Go to *localhost:8000* to interact with it and *localhost:8888* for phpMyAdmin.

### 1.2. Installation with LAMP

Here is the installation with a LAMP stack. You can use the web server of your choice as long as you can make it work
with Symfony. You also need PHP 7.4, MySQL 8 and Composer.

-  Clone the repository.
-  Go to the project root.
-  Execute `composer install` in your terminal.
-  Create a *env.local* file and add your database credentials.
-  Execute `php bin/console doctrine:schema:update --force` in your terminal.
-  Execute `php bin/console doctrine:fixture:load` in your terminal.
-  Add `APP_ENV=prod` in *.env.local*.
-  Execute `rm -rf var/cache/` in your terminal.

Now the application should work.
