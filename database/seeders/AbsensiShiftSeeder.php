<?php

namespace Database\Seeders;

use App\Models\AbsensiShift;
use Illuminate\Database\Seeder;

class AbsensiShiftSeeder extends Seeder
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
                'id_group' => 1,
                'shift' => 'PAGI',
                'created_at' => now()
            ],
            [
                'id_group' => 2,
                'shift' => 'MALAM',
                'created_at' => now()
            ],
            [
                'id_group' => 3,
                'shift' => 'SIANG',
                'created_at' => now()
            ],
        ];
        AbsensiShift::insert($data);
    }
}
