<?php

namespace Armincms\Bios;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

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
        $resources = Nova::groupedResources(request());

        return view()->first([config("bios.navigation"), 'bios::navigation'], [
            'groupedResources' => collect($resources)->map(function($resources) {
                return $resources->filter(function($resource) { 
                    return $resource::$configurable ?? false;
                });
            })->filter->count(),
        ]);
    }  
}
