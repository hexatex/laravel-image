<?php

namespace Hexatex\LaravelImage\Image\Resources\Admin;

use Hexatex\LaravelMisc\AdminAuthenticatableResources;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'alt' => $this->alt,
            'disk' => $this->disk,
            'original_filename' => $this->original_filename,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            /* Accessors & Mutators */
            'url' => $this->url,
            'thumbnail_url' => $this->thumbnail_url,

            /* Relationships */
            'author' => AdminAuthenticatableResources::single($this->whenLoaded('author')),
        ];
    }
}
