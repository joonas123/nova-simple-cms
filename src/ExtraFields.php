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

        $additionalFields = self::fetch($blueprint);

        foreach($additionalFields as $key => $field) {
            
            $field->onlyOnForms();

            // Custom delete handling form file-fields
            if($field->component == 'file-field') {
                $field->delete(new DeleteFile);
            } 

            $fields[] = $field;
        }

        return $fields;

    }

    static function fetch($blueprint)
    {

        $blueprint = "\App\Nova\Blueprints\\$blueprint";

        if(class_exists($blueprint)) {

            $blueprint = new $blueprint;

            return $blueprint->fields();

        }

        return [];

    }

    static function fieldNames($blueprint)
    {

        return array_map(function($field) {
            return $field->attribute;
        }, self::fetch($blueprint));

    }
}