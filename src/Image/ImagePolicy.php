<?php

namespace Hexatex\LaravelImage\Image;

use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelMisc\Contracts\Authenticatable;

class ImagePolicy
{
    protected $user;
    protected $image;

    public function __construct(?Authenticatable $user, Image $image)
    {
        $this->user = $user;
        $this->image = $image;
    }

    public function view()
    {
        return true; // todo is there ever a time when this should be false?
    }

    public function update()
    {
        return $this->hasPermissionOrOwnership('update');
    }

    public function destroy()
    {
        return $this->hasPermissionOrOwnership('destroy');
    }

    public function download()
    {
        return true; // todo is there ever a time when this should be false?
    }

    /*
     * Protected Methods
     */
    protected function hasPermissionOrOwnership(string $imagePermission): bool
    {
        if (!$this->user) {
            return false;
        }

        if ($this->user->hasPermissionTo("image.{$imagePermission}")) {
            return true;
        }

        return $this->userOwnsImage() && $this->user->hasPermissionTo("image.{$imagePermission}-owned");
    }

    protected function userOwnsImage()
    {
        return $this->user
            && $this->image->author
            && $this->image->author->id === $this->user->id
            && $this->image->author->getMorphClass() === $this->user->getMorphClass();
    }
}
