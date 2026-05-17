<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImageablesTable extends Migration
{
    public function up()
    {
        Schema::create('imageables', function (Blueprint $table) {
            $table->comment('See Hexatex\LaravelImage\Image\Contracts\Imageable');

            $table->foreignUlid('image_id')->references('id')->on('images')->onUpdate('cascade')->onDelete('cascade');
            $table->index('image_id');

            $table->ulidMorphs('imageable');
        });
    }

    public function down()
    {
        Schema::dropIfExists('imageables');
    }
}
