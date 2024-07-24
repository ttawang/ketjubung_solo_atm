<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSongket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_stok_penerimaan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_pengiriman_barang_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_tenun_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_inspecting_grey_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_dudulan_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_inspect_dudulan_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_jahit_sambung_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_p1_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_inspect_p1_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_finishing_cabut_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_inspect_finishing_cabut_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_jigger_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_drying_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_p2_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_inspect_p2_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('tbl_jahit_p2_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable();
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_stok_penerimaan', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
        Schema::table('tbl_pengiriman_barang_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
        Schema::table('tbl_tenun_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
        Schema::table('tbl_inspecting_grey_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
        Schema::table('tbl_dudulan_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
        Schema::table('tbl_inspect_dudulan_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
        Schema::table('tbl_jahit_sambung_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
        Schema::table('tbl_p1_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
        Schema::table('tbl_inspect_p1_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
        Schema::table('tbl_finishing_cabut_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
        Schema::table('tbl_inspect_finishing_cabut_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
        Schema::table('tbl_jigger_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
        Schema::table('tbl_drying_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
        Schema::table('tbl_p2_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
        Schema::table('tbl_inspect_p2_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
        Schema::table('tbl_jahit_p2_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
    }
}
