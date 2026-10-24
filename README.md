[![Build Status](https://travis-ci.org/janstuemmel/slim3-doctrine-testing-example.svg?branch=master)](https://travis-ci.org/janstuemmel/slim3-doctrine-testing-example)
[![Test Coverage](https://codeclimate.com/github/janstuemmel/slim3-doctrine-testing-example/badges/coverage.svg)](https://codeclimate.com/github/janstuemmel/slim3-doctrine-testing-example/coverage)

# Slim3-Doctrine Testing

Testing example for a slim3-doctrine setup with in-memory sqlite database.

## Requirements

* PHP >= 8.2.35 for the Docker image
* sqlite

## PHP2Shell remediation status: blocked

The Docker image now uses the patched `php:8.2.35-apache` base and disables
`session.upload_progress.enabled`. The application has not yet been verified
on PHP 8.2, so this change is **not ready to deploy**.

The existing `composer.lock` contains PHP 7-only Doctrine and test packages.
Composer cannot install this lockfile on PHP 8, and
`there4/slim-test-helpers` only supports PHPUnit through version 6. Resolve and
test these dependencies before treating the image as remediated. PHP 7.2 is
unsupported and is a breaking change for anyone running this container.

After the dependency migration is complete, rebuild the image and redeploy all
containers; existing containers do not receive the PHP or session-configuration
changes automatically.

## Packages

* `phpunit/phpunit`
* `there4/slim-test-helpers`

## Usage

```sh
# install deps
composer install

# test
composer test

# serve
touch app.db && composer db:create
composer serve # starts build-in webserver at localhost:8080

# add some test data
curl --data '' localhost:8080/api/posts
curl --data 'title=Hello World' localhost:8080/api/posts

# get data
curl localhost:8080/api/posts
```

... you will get:

```json
[
  {
    "id": 1,
    "title": "New post",
    "content": "Insert content here..."
  },
  {
    "id": 2,
    "title": "Hello World",
    "content": "Insert content here..."
  }
]
```
