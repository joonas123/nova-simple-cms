<?php

namespace Joonas1234\NovaSimpleCms\Http\Controllers;

use Joonas1234\NovaSimpleCms\Blueprint;
use Illuminate\Http\Request;

class BlueprintController extends Controller
{
    
    
    /**
     * Get available blueprints
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) 
    {
        if($request->editing) {
            return response(Blueprint::updateFormValues(), 200);
        }

        return response(Blueprint::creationFormValues(), 200);
    }

}