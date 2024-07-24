<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPengirimanGresik extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_pengiriman_sarung', function (Blueprint $table) {
            $table->enum('tipe', ['LOKAL', 'LUAR'])->default('LOKAL');
            $table->enum('tipe_selected', ['GRESIK', 'FINISHEDGOODS'])->nullable();
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
