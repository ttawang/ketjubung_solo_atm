<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogStokPenerimaansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_stok_penerimaan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal');
            $table->unsignedBigInteger('id_gudang')->default(1);
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_warna')->nullable();
            $table->unsignedBigInteger('id_motif')->nullable();
            $table->unsignedBigInteger('id_grade')->nullable()->comment('id tabel Kualitas');
            $table->unsignedBigInteger('id_kualitas')->nullable()->comment('id tabel Mapping Kualitas');
            $table->unsignedBigInteger('id_beam')->nullable();
            $table->unsignedBigInteger('id_mesin')->nullable();
            $table->string('is_dyeing_jasa_luar')->nullable()->comment('Dyeing Jasa Luar');
            $table->string('is_sizing')->nullable()->comment('Beam Lusi Sizing');
            $table->string('is_doubling')->nullable()->comment('Doubling');
            $table->enum('tipe_pra_tenun', ['CUCUK', 'TYEING'])->nullable()->comment('Beam Lusi Cucukan atau Tyeing');
            $table->float('volume_masuk_1')->default(0);
            $table->float('volume_keluar_1')->default(0);
            $table->unsignedBigInteger('id_satuan_1');
            $table->float('volume_masuk_2')->nullable();
            $table->float('volume_keluar_2')->nullable();
            $table->unsignedBigInteger('id_satuan_2')->nullable();
            $table->string('code');
            $table->string('is_saldoawal')->nullable();
            $table->foreign('id_gudang')->references('id')->on('tbl_gudang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_barang')->references('id')->on('tbl_barang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_1')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_2')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('tbl_penerimaan_barang_detail', function ($table) {
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
        Schema::dropIfExists('log_stok_penerimaan');
    }
}
