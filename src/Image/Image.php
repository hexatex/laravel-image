<?php

namespace Hexatex\LaravelImage\Image;

use Hexatex\LaravelImage\Image\Contracts\Image as ImageContract;
use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Hexatex\LaravelMisc\Model;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\URL;

class Image extends Model implements ImageContract
{
    protected $fillable = ['alt'];

    protected $searchable = ['alt', 'original_filename'];

    /*
     * Accessors & Mutators
     */
    public function url(): Attribute
    {
        return Attribute::get(function () {
            return $this->id ? URL::signedRoute('image.download', ['image' => $this->id]) : null;
        });
    }

    public function thumbnailUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->id ? URL::signedRoute('image.thumbnail', ['image' => $this->id]) : null;
        });
    }

    /*
     * Relationships
     */
    /**
     * @see Authenticatable
     */
    public function author(): Builder
    {
        return $this->morphTo();
    }
}
