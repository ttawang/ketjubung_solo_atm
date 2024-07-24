<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectingGreyDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_inspecting_grey_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal')->nullable();
            $table->unsignedBigInteger('id_tenun_detail');
            $table->unsignedBigInteger('id_beam');
            $table->unsignedBigInteger('id_mesin');
            $table->unsignedBigInteger('id_motif');
            $table->unsignedBigInteger('id_warna');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_gudang');
            $table->unsignedBigInteger('id_group');
            $table->unsignedBigInteger('id_satuan_1')->nullable();
            $table->float('volume_1')->nullable();
            $table->unsignedBigInteger('id_satuan_2')->nullable();
            $table->float('volume_2')->nullable();
            $table->unsignedBigInteger('id_log_stok_penerimaan');
            $table->float('jml_grade_a')->default(0);
            $table->float('jml_grade_b')->default(0);
            $table->float('jml_grade_c')->default(0);
            $table->float('jml_kualitas_1')->default(0);
            $table->float('jml_kualitas_2')->default(0);
            $table->float('jml_kualitas_3')->default(0);
            $table->float('jml_kualitas_4')->default(0);
            $table->float('jml_kualitas_5')->default(0);
            $table->float('jml_kualitas_6')->default(0);
            $table->float('jml_kualitas_7')->default(0);
            $table->float('jml_kualitas_8')->default(0);
            $table->float('jml_kualitas_9')->default(0);
            $table->float('jml_kualitas_10')->default(0);
            $table->float('jml_kualitas_11')->default(0);
            $table->float('jml_kualitas_12')->default(0);
            $table->float('jml_kualitas_13')->default(0);
            $table->float('jml_kualitas_14')->default(0);
            $table->float('jml_kualitas_15')->default(0);
            $table->float('jml_kualitas_16')->default(0);
            $table->string('code')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_tenun_detail')->references('id')->on('tbl_tenun_detail')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_beam')->references('id')->on('tbl_beam')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_mesin')->references('id')->on('tbl_mesin')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_motif')->references('id')->on('tbl_motif')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_warna')->references('id')->on('tbl_warna')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_barang')->references('id')->on('tbl_barang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_gudang')->references('id')->on('tbl_gudang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_group')->references('id')->on('tbl_group')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_1')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_satuan_2')->references('id')->on('tbl_satuan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_log_stok_penerimaan')->references('id')->on('log_stok_penerimaan')->onUpdate('set null')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_inspecting_grey_detail');
    }
}
