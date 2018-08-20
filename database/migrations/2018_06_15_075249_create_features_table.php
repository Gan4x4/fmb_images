<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('features', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('image_id')->nullable()->default(null);
            $table->string('class')->nullable()->default(null);
            $table->string('subclass')->nullable()->default(null);
            $table->json('region')->nullable()->default(null);
            $table->string('color')->nullable()->default(null);
            $table->integer('brand_id')->nullable()->default(null);
            $table->integer('model_id')->nullable()->default(null);
        });
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('features');
    }
}
