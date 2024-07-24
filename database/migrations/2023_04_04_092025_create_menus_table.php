<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('display_name')->nullable();
            $table->string('display_name_full')->nullable();
            $table->string('link')->nullable();
            $table->string('prefix')->nullable();
            $table->string('prefix_aside')->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->string('icon')->nullable();
            $table->string('sort_prefix')->nullable();
            $table->bigInteger('sort_number')->nullable();
            $table->string('is_main_nav')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
