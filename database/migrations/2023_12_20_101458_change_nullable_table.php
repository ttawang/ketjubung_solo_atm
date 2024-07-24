<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNullableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_folding_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_beam')->nullable()->change();
            $table->unsignedBigInteger('id_mesin')->nullable()->change();
        });
        Schema::table('tbl_p1_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_beam')->nullable()->change();
            $table->unsignedBigInteger('id_mesin')->nullable()->change();
        });
        Schema::table('tbl_inspect_p1_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_beam')->nullable()->change();
            $table->unsignedBigInteger('id_mesin')->nullable()->change();
        });
        Schema::table('tbl_finishing_cabut_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_beam')->nullable()->change();
            $table->unsignedBigInteger('id_mesin')->nullable()->change();
        });
        Schema::table('tbl_inspect_finishing_cabut_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_beam')->nullable()->change();
            $table->unsignedBigInteger('id_mesin')->nullable()->change();
        });
        Schema::table('tbl_jigger_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_beam')->nullable()->change();
            $table->unsignedBigInteger('id_mesin')->nullable()->change();
        });
        Schema::table('tbl_drying_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_beam')->nullable()->change();
            $table->unsignedBigInteger('id_mesin')->nullable()->change();
        });
        Schema::table('tbl_p2_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_beam')->nullable()->change();
            $table->unsignedBigInteger('id_mesin')->nullable()->change();
        });
        Schema::table('tbl_inspect_p2_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_beam')->nullable()->change();
            $table->unsignedBigInteger('id_mesin')->nullable()->change();
        });
        Schema::table('tbl_jahit_p2_detail', function (Blueprint $table) {
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
