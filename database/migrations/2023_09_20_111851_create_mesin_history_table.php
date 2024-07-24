<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMesinHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_mesin_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_mesin');
            $table->unsignedBigInteger('id_beam')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_mesin_history');
    }
}
