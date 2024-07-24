<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChemicalFinishingSarungsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_chemical_finishing_sarung', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_motif');
            $table->float('volume');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('validated_at')->nullable();
            $table->foreign('id_barang')->references('id')->on('tbl_barang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_motif')->references('id')->on('tbl_motif')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_chemical_finishing_sarung');
    }
}
