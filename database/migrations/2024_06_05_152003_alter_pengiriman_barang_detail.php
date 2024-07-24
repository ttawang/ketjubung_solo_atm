<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPengirimanBarangDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_tenun_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pengiriman_barang_detail')->nullable();
            $table->foreign('id_pengiriman_barang_detail')->references('id')->on('tbl_pengiriman_barang_detail')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_tenun_detail', function (Blueprint $table) {
            $table->dropColumn('id_pengiriman_barang_detail')->nullable();
        });
    }
}
