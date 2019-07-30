<?php 
namespace Joonas1234\NovaSimpleCmsFacade;

use Illuminate\Support\Facades\Facade;
use Joonas1234\NovaSimpleCmsFacade\PageModel;
 
class NovaSimpleCmsFacade extends Facade {
 
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PageModel::class;
    }
 
}