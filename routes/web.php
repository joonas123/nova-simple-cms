<?php

use Illuminate\Support\Facades\Route;

Route::get('/{slug}', 'Ninjami\NovaSimpleCms\Http\Controllers\PublicPageController@handle')
    ->where(['slug' => '[a-z0-9-]+', 'slug' => '^(?!nova|admin).*$']);