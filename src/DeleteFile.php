<?php

namespace Ninjami\NovaSimpleCms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeleteFile
{
    /**
     * Delete the field's underlying file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string|null  $disk
     * @param  string|null  $path
     * @return array
     */
    public function __invoke(Request $request, $model, $disk, $path)
    {

        $urlSegments = $request->segments();
        $field = array_pop($urlSegments);

        $data = $model->data;

        if(isset($data[$field])) {
            $path = $data[$field];
            $data[$field] = null;
        }

        if (! $path) {
            return;
        }    
            
        Storage::disk($disk)->delete($path);

        return [
            'data' => $data,
        ];
    }
}