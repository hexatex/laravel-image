<?php

namespace Hexatex\LaravelImage\Image\Requests\Admin;

use Hexatex\LaravelMisc\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'alt' => ['required', 'string', 'max:191'],
            'file' => [
                'required',
                File::image(),
                'min:' . config('image.min-upload-size'),
                'max:' . config('image.max-upload-size'),
            ],
        ];
    }
}
