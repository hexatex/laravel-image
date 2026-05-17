<?php

namespace Hexatex\LaravelImage\Image\Strategies\Resizer;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image as InterventionFacade;
use Intervention\Image\Image as InterventionImage;

class DefaultResizer implements Resizer
{
    /**
     * UploadedFile to resized image file as a string
     */
    public function resize(UploadedFile $uploadedFile, int $maxWidth, int $maxHeight): string
    {
        $intervention = InterventionFacade::make($uploadedFile);
        $intervention->orientate(); // Otherwise some mobile device photos will be orientated incorrectly when uploaded.
        $intervention->encode('jpg', config('image.jpeg-quality'));

        if ($intervention->width() < $intervention->height()) { // Portrait
            if ($intervention->width() > $maxWidth) {
                $this->resizeImage($intervention, $maxWidth, null);
            }
        } else { // Landscape
            if ($intervention->height() > $maxHeight) {
                $this->resizeImage($intervention, null, $maxHeight);
            }
        }

        return (string)$intervention;
    }

    /*
     * Protected Methods
     */
    /**
     * Resize an Intervention Image
     */
    protected function resizeImage(InterventionImage $intervention, ?int $width, ?int $height): void
    {
        $intervention->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
    }
}
