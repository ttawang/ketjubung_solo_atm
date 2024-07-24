<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLenoDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_leno_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_leno');
            $table->date('tanggal')->nullable();
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_warna');
            $table->unsignedBigInteger('id_gudang');
            $table->unsignedBigInteger('id_log_stok_penerimaan');
            $table->float('volume_1')->nullable();
            $table->unsignedBigInteger('id_satuan_1')->nullable();
            $table->float('volume_2')->nullable();
            $table->unsignedBigInteger('id_satuan_2')->nullable();
            $table->string('code')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_leno')->references('id')->on('tbl_leno')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_barang')->references('id')->on('tbl_barang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_warna')->references('id')->on('tbl_warna')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_gudang')->references('id')->on('tbl_gudang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_1')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_2')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_log_stok_penerimaan')->references('id')->on('log_stok_penerimaan')->onUpdate('set null')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_leno_detail');
    }
}
