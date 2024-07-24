<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInspectingGrey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_inspecting_grey', function (Blueprint $table) {
            $table->float('panjang_sarung')->nullable();
            $table->text('keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_inspecting_grey', function (Blueprint $table) {
            $table->dropColumn('panjang_sarung')->nullable();
            $table->dropColumn('keterangan')->nullable();
        });
    }
}
