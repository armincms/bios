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
        
        list($model, $callbacks) = $resourceClass::fillForUpdate($request, $resourceClass::newModel());

        $resource = $request->newResourceWith($model); 
            
        $resource->authorizeToUpdate($request);

        $resource::validateForUpdate($request, $resource); 

        $stored = $resourceClass::store()->putMany(
            $model->toArray(), $resourceClass::storeTag()
        );

        collect($callbacks)->each->__invoke();

        if($stored === false) {
            return response(['message' => 'Some data storage failed'], 422)->throwResponse();
        } 

        return response()->json([ 
            'resource' => $model,
            'redirect' => $resourceClass::redirectAfterUpdate($request, $resourceClass),
        ]);
    } 
}
