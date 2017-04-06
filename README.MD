# Laravel Settings
This package will allow managing of laravel config variables, from the database.

## Requirements
+ Laravel 5.1+
+ Cache with tags support, e.g redis, memcached, etc


## Installation
+ Install the package from composer

`composer install leantony/laravel_settings:dev-master`

+ Add the service provider to app.php

```php 
Leantony\Settings\Providers\ServiceProvider::class
```

+ Publish configuration, and migration

```php
php artisan vendor:publish --provider="Leantony\Settings\Providers\ServiceProvider" --tag="config"
```
> This will copy `app_settings.php` into the config directory

```php
php artisan vendor:publish --provider="Leantony\Settings\Providers\ServiceProvider" --tag="migrations"
```
> This will copy the migrations over to your migrations

> By default, only the values in `app.php` and `mail.php` will be included in the database. So be sure to read through and edit the file `app_settings.php` file, as you wish

+ Run migrations `php artisan migrate`

+ You're done!

## Usage
The installation on its own does not persist the config values to the database. To persist the values from config
files into the database, the package provides the following commands.

```php
php artisan manage:settings --setup
```
> Will persist the config values into the database according to the setup defined in `app_settings.php`

```php
php artisan manage:settings --bind
```
> Will bind settings in the database to those in actual laravel config. Hence settings in the database can also be normally accessed using the `config()` helper.

To ensure that the above is done automatically at boot, add the following method to your `AppServiceProvider` boot method.
```php 
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      // this method will ensure that settings persisted in the database are always
      // loaded into laravel config, at runtime
      app('settings')->replaceLoaded();
      
      // to sensure that the settings are refreshed when the database values change, add this method
      Leantony\Settings\SettingsHelper::observe();
    }
```

# Fetching settings
The package provides a helper method `settings()`. Use it as shown below

> If no argument is provided to the method, an instance of `Leantony\Settings\SettingsHelper` is returned


To fetch a setting from the database, with a key of `environment`, and category of `app`, you
will append the category to the key value (camelcased), as follows
```php
settings()->appEnvironment
```

You can also do it like this
```php
settings('app.environment')
```

To fetch all settings by a category:
```php
settings('app')

// or

settings()->getByCategory('app')
```
