<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMappingMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapping_menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('roles_id')->unsigned()->nullable();
            $table->bigInteger('menus_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('roles_id')->references('id')->on('roles')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('menus_id')->references('id')->on('menus')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mapping_menus');
    }
}
