<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInspectingGreyDetailKualitasTambahan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* $exist = Schema::hasColumns('tbl_inspecting_grey_detail', [
            'jml_kualitas_17',
            'jml_kualitas_18',
            'jml_kualitas_19',
            'jml_kualitas_20',
            'jml_kualitas_21',
            'jml_kualitas_22',
            'jml_kualitas_23',
            'jml_kualitas_24',
            'jml_kualitas_25',
            'jml_kualitas_26',
            'jml_kualitas_27',
            'jml_kualitas_28',
            'jml_kualitas_29',
            'jml_kualitas_30',
            'jml_kualitas_31',
            'jml_kualitas_32',
            'jml_kualitas_35',
        ]);
        if (!$exist) {
            Schema::create('tbl_inspecting_grey_detail', function (Blueprint $table) {
                $table->float('jml_kualitas_17')->default(0);
                $table->float('jml_kualitas_18')->default(0);
                $table->float('jml_kualitas_19')->default(0);
                $table->float('jml_kualitas_20')->default(0);
                $table->float('jml_kualitas_21')->default(0);
                $table->float('jml_kualitas_22')->default(0);
                $table->float('jml_kualitas_23')->default(0);
                $table->float('jml_kualitas_24')->default(0);
                $table->float('jml_kualitas_25')->default(0);
                $table->float('jml_kualitas_26')->default(0);
                $table->float('jml_kualitas_27')->default(0);
                $table->float('jml_kualitas_28')->default(0);
                $table->float('jml_kualitas_29')->default(0);
                $table->float('jml_kualitas_30')->default(0);
                $table->float('jml_kualitas_31')->default(0);
                $table->float('jml_kualitas_32')->default(0);
                $table->float('jml_kualitas_35')->default(0);
            });
        } */
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
