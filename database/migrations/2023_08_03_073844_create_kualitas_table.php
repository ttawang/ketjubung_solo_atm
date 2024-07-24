<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKualitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kualitas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('grade');
            $table->string('alias')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('tbl_pengiriman_barang_detail', function ($table) {
            $table->foreign('id_grade')->references('id')->on('tbl_kualitas')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('log_stok_penerimaan', function ($table) {
            $table->foreign('id_grade')->references('id')->on('tbl_kualitas')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_kualitas');
    }
}
