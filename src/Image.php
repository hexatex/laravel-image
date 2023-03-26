<?php

namespace Hexatex\LaravelImage\Models;

use Hexatex\LaravelFiltered\Filtered;
use Hexatex\LaravelHashId\HasHashId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

/**
 * App\Models\Image
 *
 * @property int $id
 * @property string $original_filename
 * @property string $path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $alt
 * @property string $thumb_path
 * @property-read mixed $thumbnail_url
 * @property-read mixed $url
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image filter($filters = [])
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image whereAlt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image whereOriginalFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image whereThumbPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Image extends Model
{
    use Filtered, HasHashId;

    protected $fillable = ['alt'];

    protected $searchable = ['alt', 'original_filename'];

    /*
     * Accessors & Mutators
     */
    public function downloadUrl(): Attribute
    {
        return Attribute::get(fn () => URL::signedRoute('image.download', ['image' => $this->id]));
    }

    public function thumbnailUrl(): Attribute
    {
        return Attribute::get(fn () => URL::signedRoute('image.thumbnail', ['image' => $this->id]));
    }
}
