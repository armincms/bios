<?php

namespace Armincms\Bios\Http\Controllers;

use Illuminate\Routing\Controller;
use Armincms\Bios\Http\Requests\BiosRequest;

class UpdateFieldController extends Controller
{
    /**
     * List the update fields for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function index(BiosRequest $request)
    { 
        $resourceClass = $request->newResource();

        $resourceClass->authorizeToUpdate($request);

        return response()->json([
            'fields' => $resourceClass->updateFieldsWithinPanels($request, $request->newResource()),
            'panels' => $resourceClass->availablePanelsForUpdate($request, $request->newResource()),
        ]);
    }
}
