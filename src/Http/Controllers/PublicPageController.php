<?php

namespace Joonas1234\NovaSimpleCms\Http\Controllers;

use Joonas1234\NovaSimpleCms\PageModel;
use Illuminate\Support\Str;

class PublicPageController extends Controller
{
 
    /**
     * Find and display public page
     *
     * @param string $slug
     * @return view
     */
    public function handle($slug) 
    {
        $page = PageModel::where('slug', $slug)->firstOrFail();

        return view('nova-simple-cms::templates.' . Str::kebab($page->blueprint), [
            'page' => $page->viewData
        ]);
    }

}