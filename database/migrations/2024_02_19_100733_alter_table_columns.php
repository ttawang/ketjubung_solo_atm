<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_warping_detail', function (Blueprint $table) {
            $table->timestamp('returned_at')->nullable();
        });

        Schema::table('tbl_motif', function (Blueprint $table) {
            $table->string('owner')->default('SOLO');
        });

        Schema::table('tbl_barang', function (Blueprint $table) {
            $table->string('owner')->default('SOLO');
        });

        Schema::table('tbl_penerimaan_sarung_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_warna')->nullable()->change();
        });

        Schema::table('tbl_jigger_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_warna')->nullable()->change();
        });

        Schema::table('tbl_drying_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_warna')->nullable()->change();
        });

        Schema::table('tbl_pengiriman_sarung_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_warna')->nullable()->change();
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
