<?php

namespace Joonas1234\NovaSimpleCms\Http\Controllers;

use Joonas1234\NovaSimpleCms\Http\Resources\PageViewResource;
use Joonas1234\NovaSimpleCms\PageModel;

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

        return view('nova-simple-cms::templates.'.$page->blueprint, [
            'page' => new PageViewResource($page)
        ]);
    }

}