# Laravel Settings
This package will allow managing of laravel config variables, from the database.

## Requirements
+ Laravel 5.1+
+ Cache with tags support, e.g redis, memcached, etc


## Installation
+ Install the package from composer

`composer install leantony/laravel_settings`

+ Add the service provider to app.php

`Leantony\Settings\Providers\ServiceProvider::class`

+ Publish configuration, and migration

`php artisan vendor:publish --provider="Leantony\Settings\Providers\ServiceProvider" --tag="config"`
> This will copy `app_settings.php` into the config directory

`php artisan vendor:publish --provider="Leantony\Settings\Providers\ServiceProvider" --tag="migrations"`
> This will copy the migrations over to your migrations

> By default, only the values in `app.php` and `mail.php` will be included in the database. So be sure to read through and edit the file `app_settings.php` file, as you wish

+ Run migrations `php artisan migrate`

+ You're done!

## Usage
The installation on its own does not persist the config values to the database. To persist the values from config
files into the database, the package provides the following commands.

`php artisan manage:settings --setup=true`
> Will persist the config values into the database according to the setup defined in `app_settings.php`

However, laravel will still load the config values from the files directly. Which is not what we want.
We want laravel to replace those loaded from files, with the ones we have in our database at runtime. To achieve this, do the following
+ Add the following method to your `AppServiceProvider` boot method.

`app('settings')->replaceLoaded();`