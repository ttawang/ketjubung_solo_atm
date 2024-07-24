<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNullable2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_dudulan_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_beam')->nullable()->change();
            $table->unsignedBigInteger('id_mesin')->nullable()->change();
        });
        Schema::table('tbl_inspect_dudulan_detail', function (Blueprint $table) {
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
