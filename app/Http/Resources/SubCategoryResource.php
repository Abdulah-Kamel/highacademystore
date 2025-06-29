<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $translations = $this->translations->pluck('title', 'locale')->toArray();

        return [
            'id' => $this->id,
            'title' => $translations,
            'parent_id' => $this->parent_id,
            'photo' => $this->photo,
        ];
    }
}
