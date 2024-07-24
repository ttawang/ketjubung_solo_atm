<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokopnameDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_stokopname_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_stokopname');
            $table->unsignedBigInteger('id_gudang');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_warna')->nullable();
            $table->unsignedBigInteger('id_motif')->nullable();
            $table->unsignedBigInteger('id_beam')->nullable();
            $table->unsignedBigInteger('id_mesin')->nullable();
            $table->unsignedBigInteger('id_grade')->nullable();
            $table->unsignedBigInteger('id_kualitas')->nullable();
            $table->unsignedBigInteger('id_log_stok');
            $table->float('stok_1');
            $table->float('stokopname_1');
            $table->float('selisih_1');
            $table->unsignedBigInteger('id_satuan_1');
            $table->float('stok_2')->nullable();
            $table->float('stokopname_2')->nullable();
            $table->float('selisih_2')->nullable();
            $table->unsignedBigInteger('id_satuan_2')->nullable();
            $table->text('catatan')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->foreign('id_stokopname')->references('id')->on('tbl_stokopname')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_gudang')->references('id')->on('tbl_gudang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_barang')->references('id')->on('tbl_barang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_warna')->references('id')->on('tbl_warna')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_motif')->references('id')->on('tbl_motif')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_grade')->references('id')->on('tbl_kualitas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_kualitas')->references('id')->on('tbl_mapping_kualitas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_1')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_2')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
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
        Schema::dropIfExists('tbl_stokopname_detail');
    }
}
