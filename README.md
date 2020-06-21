# content-manager

This tool allows you to manage content for articles

## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require aiman/content-manager
```

Then you should publish the service provider, migrate database:

```bash
php artisan vendor:publish --provider="Aiman\ContentManager\ToolServiceProvider"
php artisan migrate
```

## Usage

Next up, you must register the tool with Nova. This is typically done in the tools method of the NovaServiceProvider.

```php
// in app/Providers/NovaServiceProvider.php

// ...

public function tools()
{
    return [
        // ...
        new \Aiman\ContentManager\ContentManager(),
    ];
}
```


## Cofiguration

by Default Content Model will be used in the package. Although Article Model should be created and configured in the config file

```
<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Default Options
    |--------------------------------------------------------------------------
    |
    | Here you can define the options that are passed to all NovaTinyMCE
    | fields by default. Override these values from options method when using fields.
    |
    */

    'article_model' => \App\Models\Article::class,
    'content_model' => \Aiman\ContentManager\Http\Models\Content::class,
    'storage_url' => env('SPACES') //storagel path used for showing content images
];
?>
```

## Important
This package is tested for **Nova v2.0+**
Latest tested on **nova v3.6.0**

## Credit
Huge Credit goes for [@Jawish Hameed](https://github.com/jawish) for his thaana translation plugin [Thaana Keyboard](https://github.com/jawish/jtk)
