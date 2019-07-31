<?php

namespace Joonas1234\NovaSimpleCms;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class Blueprint
{

    /**
     * Indicates if the resource should be selectable in the form
     *
     * @var bool
     */
    public static $showInForm = true;

    /**
     * Get the template folder of this blueprint
     *
     * @return string|null
     */
    public static function templateFolder() { }

    /**
     * Get the template name of this blueprint
     *
     * @return string|null
     */
    public static function templateName() { }
    
    /**
     * Get the label of blueprint
     *
     * @return string
     */
    public static function label() 
    {
        return class_basename(get_called_class());
    }

    /**
     * Get the url prefix for the slug
     *
     * @return string
     */
    public static function slugPrefix() { }

    /**
     * Load specified blueprint class
     *
     * @return string
     */
    public static function loadBlueprint($className) 
    {
        $className = str_replace('/', '\\', 'App/' . config('nova.simple_cms.blueprint_folder') . '/' . $className);
        return class_exists($className) 
            ? new $className 
            : new self;
    }

    /**
     * Get available blueprint keys and labels for creation form
     *
     * @return array
     */
    public static function creationFormBlueprints() 
    {   
        $classes = self::get();

        $formValues = [];

        foreach($classes as $className) {
            
            $class = 'App/' . config('nova.simple_cms.blueprint_folder') . '/' . $className;
            $class = str_replace('/', '\\', $class);

            if($class::$showInForm)
                $formValues[$className] = $class::label();

        }
        return $formValues;
    }

    /**
     * Get available blueprint keys and labels for edit form
     *
     * @return array
     */
    public static function updateFormBlueprints() 
    {   
        $classes = self::get();

        $formValues = [];

        foreach($classes as $className) {
            
            $class = 'App/' . config('nova.simple_cms.blueprint_folder') . '/' . $className;
            $class = str_replace('/', '\\', $class);

            $formValues[$className] = $class::label();

        }
        return $formValues;
    }

    /**
     * Get all blueprints
     *
     * @return array
     */
    static function get() 
    {
        
        $filesystem = new Filesystem;
        
        $folder = config('nova.simple_cms.blueprint_folder');
        $files = $filesystem->files(app_path($folder));

        $blueprints = array_map(function($file) {
            return Str::before($file->getRelativePathname(), '.php');
        }, $files);

        return $blueprints;
    }

}