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
    'storage_url' => env('SPACES')
];
?>
