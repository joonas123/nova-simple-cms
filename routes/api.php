<?php

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

// Route::get('/endpoint', function (Request $request) {
//     //
// });

Route::get('/blueprints', 'Ninjami\NovaSimpleCms\Http\Controllers\BlueprintController@index');

Route::post('/{resource}', 'Ninjami\NovaSimpleCms\Http\Controllers\CreateController@handle');

Route::get('/{resource}/creation-fields', 'Ninjami\NovaSimpleCms\Http\Controllers\CreateController@formFields');

Route::get('/{resource}/{resourceId}/update-fields', 'Ninjami\NovaSimpleCms\Http\Controllers\UpdateController@formFields');

Route::put('/{resource}/{resourceId}', 'Ninjami\NovaSimpleCms\Http\Controllers\UpdateController@handle');

