<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenerimaanSarungDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_penerimaan_sarung_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_penerimaan_sarung');
            $table->unsignedBigInteger('id_gudang');
            $table->unsignedBigInteger('id_beam')->nullable();
            $table->unsignedBigInteger('id_mesin')->nullable();
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_warna');
            $table->unsignedBigInteger('id_motif');
            $table->unsignedBigInteger('id_grade')->nullable();
            $table->unsignedBigInteger('id_kualitas')->nullable();
            $table->unsignedBigInteger('id_log_stok');
            $table->float('volume_1')->nullable();
            $table->unsignedBigInteger('id_satuan_1');
            $table->text('catatan')->nullable();
            $table->string('code');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_penerimaan_sarung')->references('id')->on('tbl_penerimaan_sarung')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_log_stok')->references('id')->on('log_stok_penerimaan')->onUpdate('set null')->onDelete('set null');
            $table->foreign('id_gudang')->references('id')->on('tbl_gudang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_barang')->references('id')->on('tbl_barang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_warna')->references('id')->on('tbl_warna')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_motif')->references('id')->on('tbl_motif')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_grade')->references('id')->on('tbl_kualitas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_kualitas')->references('id')->on('tbl_mapping_kualitas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_1')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_penerimaan_sarung_detail');
    }
}
