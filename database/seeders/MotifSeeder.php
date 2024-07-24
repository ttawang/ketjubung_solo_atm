<?php

namespace Database\Seeders;

use App\Models\Motif;
use Illuminate\Database\Seeder;

class MotifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input[0]['name'] = 'SGJ';
        $input[0]['alias'] = 'SGJ';
        $input[0]['created_by'] = 1;
        $input[0]['created_at'] = date('Y-m-d H:i:s');
        $input[0]['updated_at'] = date('Y-m-d H:i:s');

        Motif::insert($input);
    }
}
