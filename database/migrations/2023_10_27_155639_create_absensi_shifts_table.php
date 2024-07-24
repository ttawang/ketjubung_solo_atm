<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensiShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_absensi_shift', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_group');
            $table->enum('shift', ['PAGI', 'MALAM', 'SIANG']);
            $table->timestamps();
            $table->foreign('id_group')->references('id')->on('tbl_group')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absensi_shifts');
    }
}
