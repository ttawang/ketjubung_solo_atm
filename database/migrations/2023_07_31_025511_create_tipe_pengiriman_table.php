<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateTipePengirimanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_tipe_pengiriman', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('title');
            $table->string('initial');
            $table->enum('is_aktif', ['YA', 'TIDAK'])->default('YA');
            $table->unsignedBigInteger('id_gudang_asal')->default(1);
            $table->unsignedBigInteger('id_gudang_tujuan')->default(1);
            $table->integer('roles_id')->nullable();
            $table->integer('roles_id_tujuan')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_gudang_asal')->references('id')->on('tbl_gudang')->onUpdate('set null')->onDelete('set null');
            $table->foreign('id_gudang_tujuan')->references('id')->on('tbl_gudang')->onUpdate('set null')->onDelete('set null');
        });

        Schema::table('tbl_pengiriman_barang', function ($table) {
            $table->foreign('id_tipe_pengiriman')->references('id')->on('tbl_tipe_pengiriman')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_tipe_pengiriman');
    }
}
