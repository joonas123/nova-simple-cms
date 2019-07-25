<?php

namespace Joonas1234\NovaSimpleCms;

use Illuminate\Http\Request;
use Joonas1234\NovaSimpleCms\DeleteFile;

class ExtraFields
{

    /**
     * Add fields from blueprint
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $fields
     * @return array
     */
    static function merge(Request $request, $fields, $resource)
    {

        $blueprint = $request->blueprint ?? $resource->blueprint;

        if($blueprint) {

            $additionalFields = config('blueprints.' . $blueprint . '.fields');
            foreach($additionalFields as $name => $options) {
               
                $novaField = 'Laravel\Nova\Fields\\' . $options['type'];
                $novaField = $novaField::make(__(ucfirst($name)), $name)->onlyOnForms();
    
                // Custom delete handling form File and Image -fields
                if(in_array($options['type'], ['File', 'Image'])) {
                    $novaField->delete(new DeleteFile);
                } 
    
                $fields[] = $novaField;
            }

        }
        
        return $fields;

    }
}