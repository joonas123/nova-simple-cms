<?php

namespace Joonas1234\NovaSimpleCms\Http\Controllers;

class BlueprintController extends Controller
{
    
    /**
     * Get available blueprints
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return response(config('blueprints'), 200);
    }


}