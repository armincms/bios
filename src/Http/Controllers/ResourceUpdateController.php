<?php

namespace Armincms\Bios\Http\Controllers;

use Illuminate\Routing\Controller;  
use Laravel\Nova\Actions\ActionEvent;
use Armincms\Bios\Http\Requests\BiosRequest; 

class ResourceUpdateController extends Controller
{
    /**
     * Create a new resource.
     *
     * @param  \Armincms\Bios\Http\Requests\BiosRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(BiosRequest $request)
    {  
        $resourceClass = $request->newResource();
        $fields = $resourceClass::fillForUpdate($request, $resourceClass::newModel());

        $fields->each(function($value, $key) use ($request, $resourceClass) { 
            $resource = $request->newResourceWith( $resourceClass::fillModel($key, $value) ); 
            
            $resource->authorizeToUpdate($request);

            $resource::validateForUpdate($request, $resource);
            
            // ActionEvent::forResourceUpdate($request->user(), $model)->save(); 
        }); 

        $stored = $resourceClass::store()->putMany(
            $fields->toArray(), $resourceClass::storeTag()
        );

        if($stored === false) {
            return response(['message' => 'Some data storage failed'], 422)->throwResponse();
        } 

        return response()->json([ 
            'resource' => $fields->toArray(),
            'redirect' => $resourceClass::redirectAfterUpdate($request, $resourceClass),
        ]);
    } 
}
