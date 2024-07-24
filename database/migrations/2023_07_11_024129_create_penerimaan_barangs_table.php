<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenerimaanBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_penerimaan_barang', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_po')->nullable();
            $table->date('tanggal_po')->nullable();
            $table->date('tanggal_terima')->nullable();
            $table->unsignedBigInteger('id_supplier');
            $table->string('no_kendaraan')->nullable();
            $table->string('supir')->nullable();
            $table->string('no_ttbm')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamp('validated_at')->nullable();
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
        Schema::dropIfExists('tbl_penerimaan_barang');
    }
}
