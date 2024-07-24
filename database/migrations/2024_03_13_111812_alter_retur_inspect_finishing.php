<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterReturInspectFinishing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_dudulan_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_inspect_retur')->nullable();
        });

        Schema::table('tbl_p1_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_inspect_retur')->nullable();
        });

        Schema::table('tbl_finishing_cabut_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_inspect_retur')->nullable();
        });

        Schema::table('tbl_p2_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_inspect_retur')->nullable();
        });

        Schema::table('tbl_pengiriman_dyeing_gresik', function (Blueprint $table) {
            $table->string('tipe')->default('BDG');
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
