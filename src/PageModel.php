<?php
namespace Joonas1234\NovaSimpleCms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageModel extends Model
{
    
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $casts = [
        'data' => 'json'
    ];

    protected $table = 'pages';

	public function draft() {
		return $this->hasOne(PageModel::class, 'parent_id');
	}

	public function parent() {
		return $this->belongsTo(PageModel::class, 'parent_id');
	}

}