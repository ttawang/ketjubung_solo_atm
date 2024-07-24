<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectingGrey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_inspecting_grey', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal')->nullable();
            $table->unsignedBigInteger('id_beam');
            $table->unsignedBigInteger('id_songket');
            $table->unsignedBigInteger('id_mesin');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_motif');
            $table->unsignedBigInteger('id_warna');
            $table->unsignedBigInteger('id_gudang');
            $table->unsignedBigInteger('id_grade')->nullable();
            $table->unsignedBigInteger('id_kualitas')->nullable();
            $table->unsignedBigInteger('id_satuan_1');
            $table->float('volume_1');
            $table->unsignedBigInteger('id_satuan_2')->nullable();
            $table->float('volume_2')->nullable();
            $table->string('code');
            $table->unsignedBigInteger('id_log_stok_penerimaan_keluar');
            $table->unsignedBigInteger('id_log_stok_penerimaan_masuk');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_songket')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_barang')->references('id')->on('tbl_barang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_motif')->references('id')->on('tbl_motif')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_warna')->references('id')->on('tbl_warna')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_gudang')->references('id')->on('tbl_gudang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_grade')->references('id')->on('tbl_kualitas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_kualitas')->references('id')->on('tbl_mapping_kualitas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_1')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_2')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_log_stok_penerimaan_keluar')->references('id')->on('log_stok_penerimaan')->onUpdate('set null')->onDelete('set null');
            $table->foreign('id_log_stok_penerimaan_masuk')->references('id')->on('log_stok_penerimaan')->onUpdate('set null')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_inspecting_grey');
    }
}
