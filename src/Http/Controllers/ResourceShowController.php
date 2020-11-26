<?php

namespace Armincms\Bios\Http\Controllers;
 
use Illuminate\Routing\Controller; 
use Armincms\Bios\Http\Requests\BiosRequest;  

class ResourceShowController extends Controller
{
    /**
     * Display the resource for administration.
     *
     * @param  \Armincms\Bios\Http\Requests\BiosRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(BiosRequest $request)
    {
        $resource = $request->newResource();

        $resource->authorizeToView($request);

        return response()->json([
            'panels'    => $resource->availablePanelsForDetail($request, $request->newResource()),
            'resource'  => $resource->serializeForDetail($request, $request->newResource()),
        ]);
    } 
}
