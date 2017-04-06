# Laravel Settings
This package will allow managing of laravel config variables, from the database.

## Requirements
+ Laravel 5.1+
+ Cache with tags support, e.g redis, memcached, etc


## Installation
+ Install the package from composer

`composer install leantony/laravel_settings`

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

However, laravel will still load the config values from the files directly. Which is not what we want.
We want laravel to replace those loaded from files, with the ones we have in our database at runtime. To achieve this, do the following
+ Add the following method to your `AppServiceProvider` boot method.

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

+ To fetch a setting value, use the helper method provided `setting($key = null, $value = null)`
> If no argument is provided, an instance of `Leantony\Settings\SettingsHelper` is returned

# Fetching settings
The package provides a helper method `settings()`. Use it as shown below


To fetch a setting from the database, with a key of `environment`, and category of `app`, you
will append the category to the key value (camelcased), as follows
```php
settings()->appEnvironment
```

You can also do it like this
```php
settings('app.environment)
```

To fetch all settings by a category:
```php
settings('app')

// or

settings()->getByCategory('app')
```