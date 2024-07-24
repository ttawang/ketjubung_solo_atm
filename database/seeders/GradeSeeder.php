<?php

namespace Database\Seeders;

use App\Models\Kualitas;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input[0]['grade'] = 'A';
        $input[0]['created_by'] = 1;
        $input[0]['alias'] = 'Normal';
        $input[0]['created_at'] = date('Y-m-d H:i:s');
        $input[0]['updated_at'] = date('Y-m-d H:i:s');

        $input[1]['grade'] = 'B';
        $input[1]['alias'] = 'Cacat Ringan';
        $input[1]['created_by'] = 1;
        $input[1]['created_at'] = date('Y-m-d H:i:s');
        $input[1]['updated_at'] = date('Y-m-d H:i:s');

        $input[2]['grade'] = 'C';
        $input[2]['created_by'] = 1;
        $input[2]['alias'] = 'Cacat Berat';
        $input[2]['created_at'] = date('Y-m-d H:i:s');
        $input[2]['updated_at'] = date('Y-m-d H:i:s');

        Kualitas::insert($input);
    }
}
