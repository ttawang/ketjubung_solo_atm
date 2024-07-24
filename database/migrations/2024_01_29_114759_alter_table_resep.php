<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableResep extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_resep', function (Blueprint $table) {
            $table->unsignedBigInteger('id_warna')->nullable();
            $table->unsignedBigInteger('id_barang')->nullable();

            $table->foreign('id_barang')->references('id')->on('tbl_barang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_warna')->references('id')->on('tbl_warna')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
