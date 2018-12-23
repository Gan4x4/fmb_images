<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('builds', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('dataset_id')->nullable(true)->default(null);
            $table->text('params')->nullable(true)->default(null);
            $table->integer('state')->nullable(true)->default(null);
            $table->string('dir')->nullable(true)->default(null);
            $table->string('file')->nullable(true)->default(null);
            $table->string('description')->nullable(true)->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('builds');
    }
}
