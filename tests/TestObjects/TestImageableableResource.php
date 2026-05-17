<?php

namespace Hexatex\LaravelImage\Tests\TestObjects;

use Illuminate\Http\Resources\Json\JsonResource;

class TestImageableResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'morph_class' => $this->getMorphClass(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
