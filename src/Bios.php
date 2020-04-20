<?php

namespace Armincms\Bios;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Illuminate\Http\Request;

class Bios extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('bios', __DIR__.'/../dist/js/tool.js'); 
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {   
        return view()->first([config("bios.navigation"), 'bios::navigation'], [
            'groupedResources' => static::groupedResources(request()),
        ]);
    } 

    /**
     * Get the grouped resources available for the given request.
     *
     * @param  Request $request
     * @return array
     */
    public static function groupedResources(Request $request)
    {
        return collect(static::availableResources($request))
                    ->groupBy(function ($item, $key) {
                        return $item::group();
                    })->sortKeys();
    } 

    /**
     * Get the resources available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function availableResources(Request $request)
    {
        return collect(Nova::$resources)->filter(function ($resource) use ($request) {
            return  is_subclass_of($resource, Resource::class) &&
                    $resource::availableForBios($request) &&
                    $resource::authorizedToViewAny($request);
        })
            ->sortBy(Nova::sortResourcesWith())
            ->all();
    }
}
