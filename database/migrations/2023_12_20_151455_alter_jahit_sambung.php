<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterJahitSambung extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_jahit_sambung_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_beam')->nullable()->change();
            $table->unsignedBigInteger('id_mesin')->nullable()->change();
            $table->unsignedBigInteger('id_log_stok_penerimaan_keluar')->nullable()->change();
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
