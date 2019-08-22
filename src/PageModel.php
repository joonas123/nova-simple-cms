<?php
namespace Joonas1234\NovaSimpleCms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PageModel extends Model
{
    
    use SoftDeletes;

    protected $dates = ['deleted_at', 'published_at'];

    protected $casts = [
        'data' => 'json'
    ];

    protected $table = 'pages';

    public function draft() 
    {
		return $this->hasOne(PageModel::class, 'parent_id');
	}

    public function parent() 
    {
		return $this->belongsTo(PageModel::class, 'parent_id');
    }
       
    public function getViewDataAttribute() 
    {
        foreach($this->data as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    public function getBlueprintClassAttribute() 
    {
        $className = 'App/' . config('nova.simple_cms.blueprint_folder') . '/' . $this->blueprint;
        return str_replace('/', '\\', $className);
    }

    public function getTemplateAttribute() 
    {
        $folder = $this->blueprintClass::templateFolder() ?? config('nova.simple_cms.templates_folder');
        $file = $this->blueprintClass::templateName() ?? Str::kebab($this->blueprint);

        return $folder . '.' . $file;
    }
    
}