<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterP1Detail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_p1_detail', function (Blueprint $table) {
            $table->string('ket')->nullable()->after('code');
        });
        Schema::table('tbl_finishing_cabut_detail', function (Blueprint $table) {
            $table->string('ket')->nullable()->after('code');
        });
        Schema::table('tbl_p2_detail', function (Blueprint $table) {
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
