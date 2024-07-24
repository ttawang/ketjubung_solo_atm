<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMesinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_mesin', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('tipe')->nullable();
            $table->enum('jenis', ['DYEING', 'WARPING', 'LOOM'])->default('DYEING');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('tbl_dyeing_detail', function ($table) {
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_warping_detail', function ($table) {
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_warping', function ($table) {
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('log_stok_penerimaan', function ($table) {
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_pengiriman_barang_detail', function ($table) {
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_dudulan_detail', function ($table) {
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_inspect_dudulan_detail', function ($table) {
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_jahit_sambung_detail', function ($table) {
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_jigger_detail', function ($table) {
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_drying_detail', function ($table) {
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_p1_detail', function ($table) {
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_p2_detail', function ($table) {
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_finishing_cabut_detail', function ($table) {
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
        Schema::dropIfExists('tbl_mesin');
    }
}
