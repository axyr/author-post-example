# Test Project

## Installation

> This installation asumes PHP 8.2 and composer is installed.

```sh
git clone git@github.com:axyr/author-post-example.git
cd author-post-example
composer install
cp .env.example .env
php artisan key:generate
php artisan test
```

## Assumptions/Remarks

Author an have zero Posts: rating will be null instead of 1
No validation on rating has been added has been added, as there was not a requirement for creating posts:
We can solve this by either:
- adding input validation
- defining an Enum 1-10
All code is in English, also the database fields: no translations have been added.

In a real world use case, I would:
- put the query in a Repository class
- use a dedicated filter class

Please take a look in my crud generator repository, on how I think an API should look like:

https://github.com/axyr/laravel-tractor
