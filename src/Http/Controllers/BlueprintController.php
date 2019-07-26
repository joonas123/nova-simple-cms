<?php

namespace Joonas1234\NovaSimpleCms\Http\Controllers;

use Joonas1234\NovaSimpleCms\Blueprint;

class BlueprintController extends Controller
{
    
    /**
     * Get available blueprints
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        return response(Blueprint::get(), 200);
    }

}