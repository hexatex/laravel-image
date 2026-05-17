<?php

namespace Hexatex\LaravelImage\Factories;

use Hexatex\LaravelImage\Tests\TestObjects\TestImageable;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestImageableFactory extends Factory
{
    protected $model = TestImageable::class;

    public function definition()
    {
        return [];
    }
}
