<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPengirimanSarungDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_pengiriman_sarung_detail', function (Blueprint $table) {
            $table->float('id_songket')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_pengiriman_sarung_detail', function (Blueprint $table) {
            $table->dropColumn('id_songket')->nullable();
        });
    }
}
