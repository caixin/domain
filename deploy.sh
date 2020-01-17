#!/bin/bash

composer install
vendor/bin/openapi -o public/openapi.json app/Http/Controllers/Api
php artisan migrate
php artisan cache:clear
