<?php

namespace Database\Seeders;

use App\Models\NomorBeam;
use Illuminate\Database\Seeder;

class NomorBeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input = [
            [
                'name' => 'BEAM01',
                'alias' => 'BEAM01',
                'created_by' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'BEAM02',
                'alias' => 'BEAM02',
                'created_by' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'BEAM03',
                'alias' => 'BEAM03',
                'created_by' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'BEAM04',
                'alias' => 'BEAM04',
                'created_by' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'BEAM05',
                'alias' => 'BEAM05',
                'created_by' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'BEAM06',
                'alias' => 'BEAM06',
                'created_by' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'BEAM07',
                'alias' => 'BEAM07',
                'created_by' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'BEAM08',
                'alias' => 'BEAM08',
                'created_by' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'BEAM09',
                'alias' => 'BEAM09',
                'created_by' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'BEAM010',
                'alias' => 'BEAM010',
                'created_by' => 1,
                'created_at' => now()
            ],
        ];
        NomorBeam::insert($input);
    }
}
