<?php

namespace Joonas1234\NovaSimpleCms;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class Blueprint
{

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