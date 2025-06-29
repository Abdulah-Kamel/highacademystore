<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $translations = $this->translations->pluck('title', 'locale')->toArray();
         $lang = ($request->lang) ? $request->lang :"ar";
        return [
            'id' => $this->id,
            'title' => $this->translate($lang)->title,
            'photo' => $this->photo,
        ];
    }
}
