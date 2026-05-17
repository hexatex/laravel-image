<?php

namespace Hexatex\LaravelImage\Http\Controllers\Admin;

use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Contracts\ImageService;
use Hexatex\LaravelImage\Image\Requests\Admin\IndexRequest;
use Hexatex\LaravelMisc\Controller;
use Illuminate\Contracts\View\View;

class ImageController extends Controller
{
    public function __construct(ImageService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    public function index(IndexRequest $request): View
    {
        $images = $this->imageService->index($request->validated());

        $this->indexLoad($images);

        return view('admin.image.index', compact('images'));
    }

    public function show(Image $image): View
    {
        $this->load($image);

        return view('admin.image.show', compact('image'));
    }
}
