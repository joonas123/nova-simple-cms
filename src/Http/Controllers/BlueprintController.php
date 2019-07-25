<?php

namespace Ninjami\NovaSimpleCms\Http\Controllers;

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