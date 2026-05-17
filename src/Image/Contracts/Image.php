<?php

namespace Hexatex\LaravelImage\Image\Contracts;

use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Hexatex\LaravelMisc\Contracts\Model;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $alt
 * @property string $disk
 * @property string $original_filename
 * @property string $path
 * @property string $thumb_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * Read Only
 * @property-read string $url
 * @property-read string $thumbnail_url
 * 
 * Relationships
 * @property-read Authenticatable $author
 */
interface Image extends Model
{
    /* Accessors & Mutators */
    public function url(): Attribute;
    public function thumbnailUrl(): Attribute;

    /* Relationships */
    public function author(): Builder;
}
