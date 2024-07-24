<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTyeingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_tyeing', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal')->nullable();
            $table->unsignedBigInteger('id_beam');
            $table->unsignedBigInteger('id_mesin');
            $table->unsignedBigInteger('id_log_stok_keluar');
            $table->unsignedBigInteger('id_log_stok_masuk');
            $table->text('catatan')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_log_stok_keluar')->references('id')->on('log_stok_penerimaan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_log_stok_masuk')->references('id')->on('log_stok_penerimaan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_tyeing');
    }
}
