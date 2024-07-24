<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengirimanDyeingGresikDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_pengiriman_dyeing_gresik_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal')->nullable();
            $table->unsignedBigInteger('id_gudang');
            $table->unsignedBigInteger('id_pengiriman_dyeing_gresik');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_warna')->nullable();
            $table->unsignedBigInteger('id_log_stok')->nullable();
            $table->float('volume_1');
            $table->unsignedBigInteger('id_satuan_1');
            $table->float('volume_2')->nullable();
            $table->unsignedBigInteger('id_satuan_2')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->foreign('id_gudang')->references('id')->on('tbl_gudang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_pengiriman_dyeing_gresik')->references('id')->on('tbl_pengiriman_dyeing_gresik')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_barang')->references('id')->on('tbl_barang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_warna')->references('id')->on('tbl_warna')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_1')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_2')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_log_stok')->references('id')->on('log_stok_penerimaan')->onUpdate('set null')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_pengiriman_dyeing_gresik_detail');
    }
}
