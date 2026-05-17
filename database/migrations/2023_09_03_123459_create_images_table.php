<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->comment('See Hexatex\LaravelMisc\Contracts\Authenticatable');

            $table->ulid('id')->index();
            $table->string('alt')->nullable();
            $table->string('disk')->comment('See filesystems.disks in config/filesystems.php');
            $table->string('original_filename');
            $table->string('path');
            $table->string('thumb_path');

            $table->ulidMorphs('author');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('images');
    }
}
