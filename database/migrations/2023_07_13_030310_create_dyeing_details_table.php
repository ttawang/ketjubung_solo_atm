<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDyeingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_dyeing_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal')->nullable();
            $table->unsignedBigInteger('id_gudang');
            $table->unsignedBigInteger('id_dyeing');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_warna')->nullable();
            $table->unsignedBigInteger('id_mesin');
            $table->unsignedBigInteger('id_log_stok_keluar')->nullable();
            $table->unsignedBigInteger('id_log_stok_masuk')->nullable();
            $table->float('volume_1');
            $table->unsignedBigInteger('id_satuan_1');
            $table->float('volume_2')->nullable();
            $table->unsignedBigInteger('id_satuan_2')->nullable();
            $table->enum('status', ['SOFTCONE', 'DYEOVEN', 'OVERCONE'])->default('SOFTCONE');
            $table->unsignedBigInteger('id_bphd')->nullable()->comment('Penerimaan Barang Weaving');
            $table->integer('key')->nullable();
            $table->unsignedBigInteger('id_parent')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->foreign('id_gudang')->references('id')->on('tbl_gudang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_dyeing')->references('id')->on('tbl_dyeing')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_barang')->references('id')->on('tbl_barang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_1')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_2')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_log_stok_masuk')->references('id')->on('log_stok_penerimaan')->onUpdate('set null')->onDelete('set null');
            $table->foreign('id_log_stok_keluar')->references('id')->on('log_stok_penerimaan')->onUpdate('set null')->onDelete('set null');
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
        Schema::dropIfExists('tbl_dyeing_detail');
    }
}
