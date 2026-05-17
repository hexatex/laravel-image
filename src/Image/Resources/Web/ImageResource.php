<?php

namespace Hexatex\LaravelImage\Image\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'alt' => $this->alt,
            'original_filename' => $this->original_filename,
            
            /* Accessors & Mutators */
            'url' => $this->url,
            'thumbnail_url' => $this->thumbnail_url,
        ];
    }
}
