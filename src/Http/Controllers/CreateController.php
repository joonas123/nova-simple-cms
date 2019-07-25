<?php

namespace Joonas1234\NovaSimpleCms\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\CreateResourceRequest;

class CreateController extends Controller
{
    
    /**
     * Creation fields for the resource
     * 
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function formFields(NovaRequest $request) {

        $resourceClass = $request->resource();
        
        $resourceClass::authorizeToCreate($request);

        return response()->json([
            'fields' => $request->newResource()->creationFieldsWithinPanels($request),
            'panels' => $request->newResource()->availablePanelsForCreate($request),
        ]);

    }

    /**
     * Perform an action on the specified resources.
     *
     * @param  \Laravel\Nova\Http\Requests\CreateResourceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(CreateResourceRequest $request)
    {

        $resource = $request->resource();
        $resource::authorizeToCreate($request);

        $resource::validateForCreation($request);

        $model = DB::transaction(function () use ($request, $resource) {

            [$model, $callbacks] = $resource::fill(
                $request, $resource::newModel()
            );
            
            // Get fields for this blueprint
            $fields = array_keys(config('blueprints.' . $request->blueprint . '.fields'));

            // Construct new array from dynamic fields
            $data = [];
            foreach($fields as $field) {
                $data[$field] = $model->$field;

                // remove dynamic fields from model so model can be saved
                unset($model->$field);
            }
            // Assign data array to data column
            $model->data = $data; 

            $model->save();

            ActionEvent::forResourceCreate($request->user(), $model)->save();

            collect($callbacks)->each->__invoke();

            return $model;
        });
        

        return response()->json([
            'id' => $model->getKey(),
            'resource' => $model->attributesToArray(),
            'redirect' => $resource::redirectAfterCreate($request, $request->newResourceWith($model)),
        ], 201);
    }


}