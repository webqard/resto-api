# Resto API

This is a restaurant API.

[![License](https://img.shields.io/github/license/webqard/resto-api)](https://github.com/webqard/resto-api/blob/trunk/LICENSE)
[![Type coverage](https://shepherd.dev/github/webqard/resto-api/coverage.svg)](https://shepherd.dev/github/webqard/resto-api)
[![Minimum PHP version](https://img.shields.io/badge/php-%3E%3D8.2-%23777BB4?logo=php&style=flat)](https://www.php.net/)

**This API is not fit for production in its current state. Its features may changed without warning.**


## Installation

### Download the project :

```shellsession
user@host ~$ cd [PATH_WHERE_TO_PUT_THE_PROJECT] # E.g. ~/projects/
user@host projects$ git clone https://github.com/webqard/resto-api.git
user@host projects$ cd resto-api
```

### Install PHP dependencies :

```shellsession
user@host resto-api$ composer install -o [--no-dev]
```
The "--no-dev" option is for the production environment.

### Generate API

```shellsession
user@host resto-api$ ./vendor/bin/openapi -o ./public/api.json ./src/
```

### Create the database

```shellsession
user@host resto-api$ ./bin/console doctrine:database:create [-e test]
user@host resto-api$ ./bin/console make:migration [-e test]
user@host resto-api$ ./bin/console doctrine:migrations:migrate [--no-interaction] [-e test]
```
The "-e test" option is to for the test environment which uses Sqlite.


## Continuous integration

First, you need to install phive dependencies :
```shellsession
user@host resto-api$ phive install --trust-gpg-keys 4AA394086372C20A,12CE0F1D262429A5,31C7E470E2138192,8AC0BAA79732DD42,C5095986493B4AA0
```

### Tests

To run the tests :
```shellsession
user@host resto-api$ ./tools/phpunit -c ./ci/phpunit.xml
```
The generated outputs will be in `./ci/phpunit/`.
Look at `./ci/phpunit/html/index.html` for code coverage
and `./ci/phpunit/testdox.html` for a verbose list of passing / failing tests.

To run mutation testing, you must run PHPUnit first, then :
```shellsession
user@host resto-api$ ./tools/infection -c./ci/infection.json
```
The generated outputs will be in `./ci/infection/`.

### Static analysis

To do a static analysis :
```shellsession
user@host resto-api$ ./tools/psalm -c ./ci/psalm.xml [--report=./psalm/psalm.txt --output-format=text]
```
Use "--report=./psalm/psalm.txt --output-format=text"
if you want the output in a file instead of on screen.

### PHPDoc

To generate the PHPDoc :
```shellsession
user@host resto-api$ ./tools/phpdocumentor --config ./ci/phpdoc.xml
```
The generated HTML documentation will be in `./ci/phpdoc/`.


### Standard

All PHP files in this project follows [PSR-12](https://www.php-fig.org/psr/psr-12/).
To indent the code :
```shellsession
user@host resto-api$ ./tools/phpcbf --standard=PSR12 --extensions=php --ignore=*/Kernel.php -p ./src/ ./tests/
```
