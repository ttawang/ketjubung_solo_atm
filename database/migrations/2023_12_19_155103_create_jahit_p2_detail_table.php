<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJahitP2DetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_jahit_p2_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_jahit_p2');
            $table->date('tanggal')->nullable();
            $table->unsignedBigInteger('id_mesin');
            $table->unsignedBigInteger('id_gudang');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_warna');
            $table->unsignedBigInteger('id_motif');
            $table->unsignedBigInteger('id_beam');
            $table->unsignedBigInteger('id_grade')->nullable();
            $table->unsignedBigInteger('id_kualitas')->nullable();
            $table->unsignedBigInteger('id_log_stok_penerimaan');
            $table->float('volume_1')->nullable();
            $table->unsignedBigInteger('id_satuan_1')->nullable();
            $table->float('volume_2')->nullable();
            $table->unsignedBigInteger('id_satuan_2')->nullable();
            $table->string('code')->nullable();
            $table->string('ket')->nullable();
            $table->unsignedBigInteger('id_parent')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->foreign('id_jahit_p2')->references('id')->on('tbl_jahit_p2')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_gudang')->references('id')->on('tbl_gudang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_barang')->references('id')->on('tbl_barang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_warna')->references('id')->on('tbl_warna')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_motif')->references('id')->on('tbl_motif')->onUpdate('restrict')->onDelete('restrict');
            // $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_grade')->references('id')->on('tbl_kualitas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_kualitas')->references('id')->on('tbl_mapping_kualitas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_1')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_2')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            // $table->foreign('id_log_stok_penerimaan_keluar')->references('id')->on('log_stok_penerimaan')->onUpdate('set null')->onDelete('set null');
            $table->foreign('id_log_stok_penerimaan')->references('id')->on('log_stok_penerimaan')->onUpdate('set null')->onDelete('set null');
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
        Schema::dropIfExists('tbl_jahit_p2_detail');
    }
}
