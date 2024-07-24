<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePekerjasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_pekerja', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_group')->nullable();
            $table->string('no_register');
            $table->string('name');
            $table->string('no_hp');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('tbl_tenun_detail', function($table) {
            $table->foreign('id_pekerja')->references('id')->on('tbl_pekerja')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_pekerja');
    }
}
