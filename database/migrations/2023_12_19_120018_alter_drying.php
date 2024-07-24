<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrying extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_folding_detail', function (Blueprint $table) {
            $table->string('ket')->nullable()->after('code');
        });
        Schema::table('tbl_jigger_detail', function (Blueprint $table) {
            $table->string('ket')->nullable()->after('code');
        });
        Schema::table('tbl_drying_detail', function (Blueprint $table) {
            $table->string('ket')->nullable()->after('code');
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
