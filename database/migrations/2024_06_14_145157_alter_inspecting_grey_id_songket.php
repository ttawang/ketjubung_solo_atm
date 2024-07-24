<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInspectingGreyIdSongket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_inspecting_grey', function (Blueprint $table) {
            $table->unsignedBigInteger('id_songket')->nullable()->change();
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
            $table->unsignedBigInteger('id_songket')->change();
        });
    }
}
