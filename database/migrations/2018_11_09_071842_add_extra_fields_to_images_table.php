<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldsToImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->integer('user_id')->nullable(true)->default(null);
            $table->integer('source_id')->nullable(true)->default(null);
            $table->integer('status')->nullable(true)->default(null);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('source_id');
            $table->dropColumn('status');

        });
    }
}
