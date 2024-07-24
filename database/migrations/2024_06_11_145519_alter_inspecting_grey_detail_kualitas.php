<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInspectingGreyDetailKualitas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_inspecting_grey_detail', function (Blueprint $table) {
            $table->dropColumn('jml_kualitas_34')->nullable();
            $table->renameColumn('jml_kualitas_33', 'jml_kualitas_35');
        });
        Schema::table('tbl_inspecting_grey_detail', function (Blueprint $table) {
            $table->float('jml_kualitas_35')->default(0)->change();
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
            $table->float('jml_kualitas_34')->nullable()->after('jml_kualitas_33');
            $table->renameColumn('jml_kualitas_35', 'jml_kualitas_33');
        });
        Schema::table('tbl_inspecting_grey_detail', function (Blueprint $table) {
            $table->float('jml_kualitas_33')->default(0)->change();
        });
    }
}
