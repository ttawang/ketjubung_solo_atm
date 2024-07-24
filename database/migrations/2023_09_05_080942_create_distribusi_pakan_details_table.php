<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistribusiPakanDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_distribusi_pakan_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal');
            $table->unsignedBigInteger('id_distribusi_pakan');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_warna');
            $table->unsignedBigInteger('id_gudang');
            $table->unsignedBigInteger('id_beam');
            $table->unsignedBigInteger('id_mesin');
            $table->unsignedBigInteger('id_log_stok_keluar');
            $table->unsignedBigInteger('id_log_stok_masuk');
            $table->float('volume_1')->nullable();
            $table->unsignedBigInteger('id_satuan_1')->nullable();
            $table->float('volume_2')->nullable();
            $table->unsignedBigInteger('id_satuan_2')->nullable();
            $table->string('code')->nullable();
            $table->unsignedBigInteger('id_tenun_detail');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_distribusi_pakan')->references('id')->on('tbl_distribusi_pakan')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_barang')->references('id')->on('tbl_barang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_warna')->references('id')->on('tbl_warna')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_gudang')->references('id')->on('tbl_gudang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_1')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_2')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_log_stok_keluar')->references('id')->on('log_stok_penerimaan')->onUpdate('set null')->onDelete('set null');
            $table->foreign('id_log_stok_masuk')->references('id')->on('log_stok_penerimaan')->onUpdate('set null')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_distribusi_pakan_detail');
    }
}
