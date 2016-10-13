[![Build Status](https://travis-ci.org/janstuemmel/slim3-doctrine-testing-example.svg?branch=master)](https://travis-ci.org/janstuemmel/slim3-doctrine-testing-example)

# Slim3-Doctrine Testing

Testing example for a slim3-doctrine setup with in-memory sqlite database.

## Requirements

* php >= 5.5
* sqlite

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
