<?php

use Aiman\ContentManager\Http\Controllers\ArticleController;
use Aiman\ContentManager\Http\Controllers\ContentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::group([
    'prefix' => 'content'
], function ($router) {
    Route::post('all', [ContentController::class, 'reads']);
    Route::post('', [ContentController::class, 'create']);
    Route::put('', [ContentController::class, 'update']);
    Route::put('update-sort', [ContentController::class, 'updateSort']);
    Route::post('delete', [ContentController::class, 'delete']);
});

Route::group([
    'prefix' => 'article'
], function ($router) {
    Route::post('all', [ArticleController::class, 'reads']);
});



