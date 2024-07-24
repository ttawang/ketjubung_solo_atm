<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'urutan' => 1,
                'code' => 'PB',
                'nama' => 'Penerimaan Barang',
                'alias' => 'Penerimaan Barang'
            ],
            [
                'urutan' => 2,
                'code' => 'BBD',
                'nama' => 'Bahan Baku Dyeing',
                'alias' => 'Bahan Baku Dyeing'
            ],
            [
                'urutan' => 3,
                'code' => 'DS',
                'nama' => 'Dyeing Softcone',
                'alias' => 'Dyeing Softcone'
            ],
            [
                'urutan' => 4,
                'code' => 'DD',
                'nama' => 'Dyeing Dye & Oven',
                'alias' => 'Dyeing Dye & Oven'
            ],
            [
                'urutan' => 5,
                'code' => 'DW',
                'nama' => 'Dyeing Warna',
                'alias' => 'Dyeing Warna'
            ],
            [
                'urutan' => 6,
                'code' => 'DO',
                'nama' => 'Dyeing Overcone',
                'alias' => 'Dyeing Overcone'
            ],
            [
                'urutan' => 7,
                'code' => 'BHD',
                'nama' => 'Barang Hasil Dyeing',
                'alias' => 'Barang Hasil Dyeing'
            ],
            [
                'urutan' => 8,
                'code' => 'BHDR',
                'nama' => 'Benang Warna Retur',
                'alias' => 'Benang Warna Retur'
            ],
            [
                'urutan' => 8,
                'code' => 'BBDG',
                'nama' => 'Barang Baku Dyeing Gresik',
                'alias' => 'Barang Baku Dyeing Gresik'
            ],
            [
                'urutan' => 9,
                'code' => 'BDG',
                'nama' => 'Barang Dyeing Gresik',
                'alias' => 'Barang Dyeing Gresik'
            ],
            [
                'urutan' => 9,
                'code' => 'BBW',
                'nama' => 'Benang Baku Warping',
                'alias' => 'Benang Warna'
            ],
            [
                'urutan' => 10,
                'code' => 'BBWS',
                'nama' => 'Benang Baku Warping Sisa',
                'alias' => 'Benang Warna Sisa'
            ],
            [
                'urutan' => 11,
                'code' => 'BL',
                'nama' => 'Beam Lusi',
                'alias' => 'Beam Lusi'
            ],
            [
                'urutan' => 12,
                'code' => 'BS',
                'nama' => 'Beam Songket',
                'alias' => 'Beam Songket'
            ],
            [
                'urutan' => 13,
                'code' => 'BPR',
                'nama' => 'Barang Pakan Rappier',
                'alias' => 'Barang Pakan Rappier'
            ],
            [
                'urutan' => 14,
                'code' => 'BPS',
                'nama' => 'Barang Pakan Shuttle',
                'alias' => 'Barang Pakan Shuttle'
            ],
            [
                'urutan' => 15,
                'code' => 'BBPS',
                'nama' => 'Barang Pakan Sisa',
                'alias' => 'Barang Pakan Sisa'
            ],
            [
                'urutan' => 16,
                'code' => 'DPR',
                'nama' => 'Distribusi Pakan Rappier',
                'alias' => 'Distribusi Pakan Rappier'
            ],
            [
                'urutan' => 17,
                'code' => 'DPS',
                'nama' => 'Distribusi Pakan Shuttle',
                'alias' => 'Distribusi Pakan Shuttle'
            ],
            [
                'urutan' => 18,
                'code' => 'BO',
                'nama' => 'Barang Leno',
                'alias' => 'Barang Leno'
            ],
            [
                'urutan' => 19,
                'code' => 'BBTL',
                'nama' => 'Barang Baku Tenun Lusi',
                'alias' => 'Barang Baku Tenun Lusi'
            ],
            [
                'urutan' => 20,
                'code' => 'BBTS',
                'nama' => 'Barang Baku Tenun Songket',
                'alias' => 'Barang Baku Tenun Songket'
            ],
            [
                'urutan' => 21,
                'code' => 'BG',
                'nama' => 'Barang Sarung',
                'alias' => 'Barang Sarung'
            ],
            [
                'urutan' => 22,
                'code' => 'BBG',
                'nama' => 'Barang Baku Sarung',
                'alias' => 'Barang Baku Sarung'
            ],
            [
                'urutan' => 23,
                'code' => 'BGIG',
                'nama' => 'Barang Sarung Inspecting Grey',
                'alias' => 'Barang Sarung Inspecting Grey'
            ],
            [
                'urutan' => 24,
                'code' => 'BGD',
                'nama' => 'Barang Sarung Dudulan',
                'alias' => 'Barang Sarung Dudulan'
            ],
            [
                'urutan' => 25,
                'code' => 'BGDH',
                'nama' => 'Barang Sarung Dudulan Hilang',
                'alias' => 'Barang Sarung Dudulan Hilang'
            ],
            [
                'urutan' => 26,
                'code' => 'BGF',
                'nama' => 'Barang Baku Sarung Finishing',
                'alias' => 'Barang Sarung Finishing'
            ],
            [
                'urutan' => 27,
                'code' => 'JS',
                'nama' => 'Jahit Sambung',
                'alias' => 'Jahit Sambung'
            ],
            [
                'urutan' => 28,
                'code' => 'FD',
                'nama' => 'Folding',
                'alias' => 'Folding'
            ],
            [
                'urutan' => 29,
                'code' => 'BGID',
                'nama' => 'Barang Sarung Inspecting Dudulan',
                'alias' => 'Barang Sarung Inspecting Dudulan'
            ],
            [
                'urutan' => 30,
                'code' => 'P1',
                'nama' => 'P1',
                'alias' => 'P1'
            ],
            [
                'urutan' => 31,
                'code' => 'P1H',
                'nama' => 'P1 Hilang',
                'alias' => 'P1 Hilang'
            ],
            [
                'urutan' => 32,
                'code' => 'IP1',
                'nama' => 'Inspect P1',
                'alias' => 'Inspect P1'
            ],
            [
                'urutan' => 33,
                'code' => 'FC',
                'nama' => 'Finishing Cabut',
                'alias' => 'Finishing Cabut'
            ],
            [
                'urutan' => 34,
                'code' => 'FCH',
                'nama' => 'Finishing Cabut Hilang',
                'alias' => 'Finishing Cabut Hilang'
            ],
            [
                'urutan' => 35,
                'code' => 'IFC',
                'nama' => 'Inspecting Finishing Cabut',
                'alias' => 'Inspecting Finishing Cabut'
            ],
            [
                'urutan' => 36,
                'code' => 'JCS',
                'nama' => 'Jigger & Cuci Sarung',
                'alias' => 'Jigger & Cuci Sarung'
            ],
            [
                'urutan' => 37,
                'code' => 'DR',
                'nama' => 'Drying',
                'alias' => 'Drying'
            ],
            [
                'urutan' => 38,
                'code' => 'P2',
                'nama' => 'P2',
                'alias' => 'P2'
            ],
            [
                'urutan' => 39,
                'code' => 'P2H',
                'nama' => 'P2 Hilang',
                'alias' => 'P2 Hilang'
            ],
            [
                'urutan' => 40,
                'code' => 'IP2',
                'nama' => 'Inspect P2',
                'alias' => 'Inspect P2'
            ],
            [
                'urutan' => 41,
                'code' => 'CF',
                'nama' => 'Chemical Finishing',
                'alias' => 'Chemical Finishing'
            ],
            /* [
                'urutan' => 41,
                'code' => 'CJ',
                'nama' => 'Chemical Jigger & Cuci Sarung',
                'alias' => 'Chemical Jigger & Cuci Sarung'
            ],
            [
                'urutan' => 42,
                'code' => 'CD',
                'nama' => 'Chemical Drying',
                'alias' => 'Chemical Drying'
            ], */
            [
                'urutan' => 42,
                'code' => 'BBTLT',
                'nama' => 'Barang Baku Tenun Lusi Turun',
                'alias' => 'Beam Lusi Turun'
            ],
            [
                'urutan' => 43,
                'code' => 'BBTST',
                'nama' => 'Barang Baku Tenun Songket Turun',
                'alias' => 'Beam Songket Turun'
            ],
            [
                'urutan' => 44,
                'code' => 'DPRT',
                'nama' => 'Distribusi Pakan Cone Turun',
                'alias' => 'Pakan Cone Turun'
            ],
            [
                'urutan' => 45,
                'code' => 'DPST',
                'nama' => 'Distribusi Pakan Palet Turun',
                'alias' => 'Pakan Palet Turun'
            ],
            [
                'urutan' => 46,
                'code' => 'BOT',
                'nama' => 'Barang Leno Turun',
                'alias' => 'Barang Leno Turun'
            ],
            [
                'urutan' => 47,
                'code' => 'BBTLR',
                'nama' => 'Barang Baku Tenun Lusi Retur',
                'alias' => 'Beam Lusi Retur'
            ],
            [
                'urutan' => 48,
                'code' => 'BBTSR',
                'nama' => 'Barang Baku Tenun Songket Retur',
                'alias' => 'Beam Songket Retur'
            ],
            [
                'urutan' => 49,
                'code' => 'DPRR',
                'nama' => 'Distribusi Pakan Cone Retur',
                'alias' => 'Pakan Cone Retur'
            ],
            [
                'urutan' => 50,
                'code' => 'DPSR',
                'nama' => 'Distribusi Pakan Palet Retur',
                'alias' => 'Pakan Palet Retur'
            ],
            [
                'urutan' => 51,
                'code' => 'BOR',
                'nama' => 'Barang Leno Retur',
                'alias' => 'Barang Leno Retur'
            ],
            [
                'urutan' => 52,
                'code' => 'BLN',
                'nama' => 'Beam Lusi Nomor Baru',
                'alias' => 'Beam Lusi Nomor Baru'
            ],
            [
                'urutan' => 53,
                'code' => 'BOR',
                'nama' => 'Beam Songket Nomor Baru',
                'alias' => 'Beam Songket Nomor Baru'
            ],
        ];
        DB::table('production_code')->insert($data);
    }
}
