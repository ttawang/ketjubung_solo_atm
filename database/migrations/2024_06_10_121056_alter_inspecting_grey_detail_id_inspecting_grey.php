<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInspectingGreyDetailIdInspectingGrey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_inspecting_grey_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tenun_detail')->nullable()->change();
            $table->unsignedBigInteger('id_log_stok_penerimaan')->nullable()->change();
            $table->unsignedBigInteger('id_inspecting_grey')->nullable();
            $table->foreign('id_inspecting_grey')->references('id')->on('tbl_inspecting_grey')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_inspecting_grey_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tenun_detail')->change();
            $table->unsignedBigInteger('id_log_stok_penerimaan')->change();
            $table->dropColumn('id_inspecting_grey')->nullable();
        });
    }
}
