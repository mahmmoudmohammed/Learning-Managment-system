# Learning Management System

## Local Environment Setup

- run  composer install command
- cp .env.example file to .env file
- create new database
- add database info in .nev file
- run php artisan key:generate command
- run php artisan migrate
- run php artisan serve to lunch project

## Swagger API Doc

- simple blog explaining swagger with laravel https://blog.quickadminpanel.com/laravel-api-documentation-with-openapiswagger/
- add L5_SWAGGER_CONST_HOST = http://localhost:8000 at the end of .env file
- run php artisan l5-swagger:generate to generate or update the swagger
- navigate to http://localhost:8000/api/documentation to check swagger documentation


## Project Structure

- all modules be located in app/Http/Controllers/Api/Modules
- every module will have:
  . Controller file
  . Model file
  . routes file

everything related to this module will be added to this folder.
