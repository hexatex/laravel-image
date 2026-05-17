<?php

namespace Hexatex\LaravelImage\Http\Controllers\Admin\Api;

use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Contracts\ImageService;
use Hexatex\LaravelImage\Image\Requests\Admin\IndexRequest;
use Hexatex\LaravelImage\Image\Requests\Admin\StoreRequest;
use Hexatex\LaravelImage\Image\Requests\Admin\UpdateRequest;
use Hexatex\LaravelImage\Image\Resources\Admin\ImageResource;
use Hexatex\LaravelMisc\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use \Auth;

class ImageController extends Controller
{
    protected $indexLoad = ['author'];
    protected $load = ['author'];

    public function __construct(ImageService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $images = $this->service->index($request->validated());

        $this->indexLoad($images);

        return ImageResource::collection($images);
    }

    public function get(Image $image): ImageResource
    {
        $this->load($image);

        return new ImageResource($image);
    }

    public function store(StoreRequest $request): ImageResource
    {
        $this->db->transaction(function () use ($request, &$image) {
            $image = $this->service->store($request->validated(), Auth::user());
        });

        $this->load($image);

        return new ImageResource($image);
    }

    public function update(UpdateRequest $request, Image $image): ImageResource
    {
        $this->db->transaction(function () use ($request, $image) {
            $this->service->update($request->validated(), $image);
        });

        $this->load($image);

        return new ImageResource($image);
    }

    public function destroy(Image $image): JsonResponse
    {
        $this->db->transaction(function () use ($image) {
            $this->service->destroy($image);
        });

        return response()->json();
    }
}
