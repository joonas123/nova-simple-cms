<?php

namespace Ninjami\NovaSimpleCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageViewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $arr = [
            'slug' => $this->slug,
            'is_visible' => $this->is_visible,
            'content' => $this->content,
            'blueprint' => $this->blueprint,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];

        foreach($this->data as $key => $value) {
            $arr[$key] = $value;
        }
        return $arr;
    }
}
