<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterJahitP2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_jahit_p2_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_inspect_retur')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_jahit_p2_detail', function (Blueprint $table) {
            $table->dropColumn('id_inspect_retur')->nullable();
        });
    }
}
