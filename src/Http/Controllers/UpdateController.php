<?php

namespace Joonas1234\NovaSimpleCms\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\ActionEvent;
use Joonas1234\NovaSimpleCms\Blueprint;
use Joonas1234\NovaSimpleCms\ExtraFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\UpdateResourceRequest;

class UpdateController extends Controller
{
    
    /**
     * List the update fields for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function formFields(NovaRequest $request)
    {
        $resource = $request->newResourceWith($request->findModelOrFail());

        $blueprint = $request->changedblueprint ?? $resource->blueprint;

        $request->request->add(['blueprint' => $blueprint]);
        $request->query->add(['blueprint' => $blueprint]);

        $resource->authorizeToUpdate($request);

        $fields = $resource->updateFields($request)->values()->all();

        $dynamicFields = ExtraFields::fieldNames($request->blueprint);

        foreach($fields as $field) {
            if(in_array($field->attribute, $dynamicFields)) {
                $field->value = $resource->data[$field->attribute] ?? null;
            }
        }

        $blueprintClass = Blueprint::loadBlueprint($blueprint);
        
        return response()->json([
            'fields' => $fields,
            'blueprint' => $blueprint,
            'blueprintOptions' => $blueprintClass->formFieldOptions()
        ]);
    }

    /**
     * Create a new resource.
     *
     * @param  \Laravel\Nova\Http\Requests\UpdateResourceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(UpdateResourceRequest $request)
    {
        $request->findResourceOrFail()->authorizeToUpdate($request);

        $resource = $request->resource();

        $resource::validateForUpdate($request);

        $model = DB::transaction(function () use ($request, $resource) {
            $model = $request->findModelQuery()->lockForUpdate()->firstOrFail();

            // Catch data column from model
            $data = $model->data;

            if ($this->modelHasBeenUpdatedSinceRetrieval($request, $model)) {
                return response('', 409)->throwResponse();
            }

            [$model, $callbacks] = $resource::fillForUpdate($request, $model);

            $fields = ExtraFields::fetch($request->blueprint);

            foreach($fields as $field) {
                $attr = $field->attribute;
                // Don't touch if field is file and nothing is posted
                if($field->component == 'file-field') {
                    $data[$attr] = $model->$attr ?? $data[$attr] ?? null;
                } else {
                    $data[$attr] = $model->$attr;
                }  

                // remove dynamic fields from model so model can be saved
                unset($model->$attr);
            }
            // Assign data array to data column
            $model->data = $data; 

            $model->save();

            ActionEvent::forResourceUpdate($request->user(), $model)->save();

            collect($callbacks)->each->__invoke();

            return $model;

        });

        return response()->json([
            'id' => $model->getKey(),
            'resource' => $model->attributesToArray(),
            'redirect' => $resource::redirectAfterUpdate($request, $request->newResourceWith($model)),
        ]);
    }

    /**
     * Determine if the model has been updated since it was retrieved.
     *
     * @param  \Laravel\Nova\Http\Requests\UpdateResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return bool
     */
    protected function modelHasBeenUpdatedSinceRetrieval(UpdateResourceRequest $request, $model)
    {
        $column = $model->getUpdatedAtColumn();

        if (! $model->{$column}) {
            return false;
        }

        return $request->input('_retrieved_at') && $model->usesTimestamps() && $model->{$column}->gt(
            Carbon::createFromTimestamp($request->input('_retrieved_at'))
        );
    }
}