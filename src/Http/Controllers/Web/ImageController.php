<?php

namespace Hexatex\LaravelImage\Http\Controllers\Web;

use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Contracts\ImageService;
use Hexatex\LaravelMisc\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use \Storage;

class ImageController extends Controller
{
    public function __construct(ImageService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    public function download(Image $image): BinaryFileResponse
    {
        return $this->downloadImage($image->path);
    }

    public function downloadThumbnail(Image $image): BinaryFileResponse
    {
        return $this->downloadImage($image->thumb_path);
    }

    /*
     * Protected Methods
     */
    protected function downloadImage(string $path): BinaryFileResponse
    {
        $fullPath = Storage::path($path);

        return response()->download($fullPath);
    }
}
