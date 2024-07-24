<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotifsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_motif', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('alias')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::table('tbl_pengiriman_barang_detail', function ($table) {
            $table->foreign('id_motif')->references('id')->on('tbl_motif')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_warping_detail', function ($table) {
            $table->foreign('id_motif')->references('id')->on('tbl_motif')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_motif');
    }
}
