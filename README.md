# content-manager

This tool allows you to manage content for articles

## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require aiman/content-manager
```

**Configuration File is a Required**
Then you should publish the service provider, migrate database:

```bash
php artisan vendor:publish --provider="Aiman\ContentManager\ToolServiceProvider"
php artisan migrate
```

### Article Schema
Example Article Schema Used in the package. Content Manager will use a display name to display the article list. So make sure to set it in `article_display_name` the configuration file
```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('headline')->nullable();
            $table->string('sub_headline')->nullable();
            $table->text('body')->nullable();
            $table->string('status')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('caption')->nullable();
            $table->boolean('anonymous')->default(false);
            $table->json('social_media')->nullable();
            $table->integer('reviewing_by')->nullable();
            $table->dateTime('reviewing_start')->nullable();
            $table->integer('reviewed_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
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
    'article_display_name' => 'headline',
    'content_model' => \Aiman\ContentManager\Http\Models\Content::class,
    'storage_url' => env('SPACES') //storagel path used for showing content images
];
?>
```

## Important
This package is tested for **Nova v2.0+**
Latest tested on **nova v3.6.0**
