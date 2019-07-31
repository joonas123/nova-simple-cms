<?php

namespace Joonas1234\NovaSimpleCms;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\KeyValue;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use Froala\NovaFroalaField\Froala;
use Outhebox\NovaHiddenField\HiddenField;
use Joonas1234\NovaSimpleCms\ExtraFields;
use Benjaminhirsch\NovaSlugField\Slug;
use Benjaminhirsch\NovaSlugField\TextWithSlug;

class Page extends BaseResource
{

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Joonas1234\NovaSimpleCms\PageModel';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'slug'
    ];

     /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {

        $selectedBlueprint = $request->blueprint ?? $this->blueprint;
        $blueprint = Blueprint::loadBlueprint($selectedBlueprint);

        $fields = [
            ID::make()
                ->sortable(),
            TextWithSlug::make(__('Name'), 'name')
                ->slug('slug'),
            Slug::make(__('Slug'), 'slug')
                ->showUrlPreview(url('/'))
                ->rules(['required', 'unique:pages,slug,{{resourceId}}'])
                ->slugPrefix($blueprint->slugPrefix() ?? '')
                ->onlyOnForms()
                ->disableAutoUpdateWhenUpdating(),
            Text::make(__('Address'), function() {
                    return '<a target="_blank" href="'. url($this->slug) .'">' . url($this->slug) . '</a>';
                })->ExceptOnForms()->asHtml(),
            Froala::make(__('Content'), 'content')
                ->withFiles('public'),
            HiddenField::make(__('Blueprint'), 'blueprint'),
            HiddenField::make(__('Data'), 'data')->onlyOnForms(),
            KeyValue::make(__('Data'), 'data')->onlyOnDetail()
                
        ];

        $fields = ExtraFields::merge($request, $fields, $this);

        return $fields;

    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

    /**
     * Return the location to redirect the user after creation.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \App\Nova\Resource $resource
     * @return string
     */
    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        return '/nova-simple-cms/'.static::uriKey().'/'.$resource->getKey();
    }

    /**
     * Return the location to redirect the user after update.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \App\Nova\Resource $resource
     * @return string
     */
    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        return '/nova-simple-cms/'.static::uriKey().'/'.$resource->getKey();
    }

}