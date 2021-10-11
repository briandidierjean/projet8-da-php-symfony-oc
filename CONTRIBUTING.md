# Contribution Guide

## 1. Getting the Project

This project uses PHP/Symfony and MySQL. For development and production consistency we use
[Docker and Docker Compose](https://www.docker.com) to get the project working that includes everything the application
needs and also a phpMyAdmin container for administrating the database.

Here are the instructions of how to get the project and making it working on a developer machine:

- Clone the repository `git@github.com:briandidierjean/projet8-da-php-symfony-oc.git`.
- Go to the project root.
- You have to override the `docker-compose.yml` file with `docker-compose.override.yml`file and add your Blackfire
   credentials or delete the section if you do not need it. You can also add a volume for the www_docker container like
   `./:/var/www/html`.
- Execute `docker-compose up` in your terminal to launch the Docker containers. Everything should be configured
   automatically (the database credentials, the installation of the Composer dependencies, etc.).
- Execute `php bin/console doctrine:schema:update --force` in www_docker container shell to generate the database 
  structure.
- Execute `php bin/console doctrine:fixture:load` in www_docker container shell to load the testing data.

## 2. Coding Guidelines

To ensure the quality and maintainability of the project, some coding style must be followed :

- The code should stick to the [PHP Strandard Recommendations](https://www.php-fig.org/psr/).
- The code should also stick to [Symfony coding standard](https://symfony.com/doc/4.4/contributing/code/standards.html).
- To help you follow these rules [Codacy](https://www.codacy.com/) is used to monitor the quality of the code when a
  pull request is submitted.

## 3. Testing

Every time you add a new functionality or fix a bug, your code should be tested with PHPUnit for unit and functional
tests. Here are our standards for testing :

- The entities should be tested with unit tests.
- The controllers should be tested with functional tests.
- Only the public methods should be tested.
- Pull request should be opened when the code passed all the tests.

## 4. Git Guidelines

To start working on a new issue, you should create a new branch locally for that issue, commit in it, then when it is
ready, push it the repository and create a pull request.

Even the Git commit messages should follow some guidelines :

- The imperative must be used for the subject.
- The subject must be capitalized and the period at the end must be dropped.
- The subject should be used to summarize the changes.
- The body should be used to described what and why have changed.