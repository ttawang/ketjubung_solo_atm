<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableGantiNomorBeam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_sizing_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_parent')->nullable();
        });

        Schema::table('tbl_beam', function (Blueprint $table) {
            $table->string('id_beam_prev')->nullable();
        });

        Schema::table('tbl_dyeing_gresik_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_mesin')->nullable();
        });

        Schema::table('tbl_pengiriman_dyeing_gresik_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_mesin')->nullable();
        });

        Schema::table('tbl_pengiriman_sarung_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_beam')->nullable()->change();
            $table->unsignedBigInteger('id_mesin')->nullable()->change();
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
