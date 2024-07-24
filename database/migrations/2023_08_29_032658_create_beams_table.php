<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_beam', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_nomor_beam')->nullable();
            $table->unsignedBigInteger('id_nomor_kikw')->nullable();
            // $table->string('no_order')->nullable();
            // $table->unsignedBigInteger('id_mesin_history')->comment('No. Loom');
            $table->enum('tipe_pra_tenun', ['CUCUK', 'TYEING'])->nullable();
            $table->enum('tipe_beam', ['LUSI', 'SONGKET'])->default('LUSI');
            $table->string('is_sizing')->nullable()->comment('Beam Lusi Sizing');
            $table->integer('finish')->default(0);
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('tbl_pengiriman_barang_detail', function ($table) {
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('tbl_cucuk', function ($table) {
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('tbl_tyeing', function ($table) {
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('tbl_tenun', function ($table) {
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('tbl_tenun_detail', function ($table) {
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('tbl_warping_detail', function ($table) {
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('tbl_sizing_detail', function ($table) {
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('tbl_dudulan_detail', function ($table) {
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('tbl_inspect_dudulan_detail', function ($table) {
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('tbl_jahit_sambung_detail', function ($table) {
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('tbl_jigger_detail', function ($table) {
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('tbl_drying_detail', function ($table) {
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('tbl_p1_detail', function ($table) {
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('tbl_p2_detail', function ($table) {
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('tbl_finishing_cabut_detail', function ($table) {
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_beam');
    }
}
