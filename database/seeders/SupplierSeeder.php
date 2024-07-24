<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input[0]['name'] = 'CV. KETJUBUNG';
        $input[0]['created_by'] = 1;
        $input[0]['created_at'] = now();
        $input[0]['updated_at'] = now();

        $input[1]['name'] = 'Dian Kimia Putra';
        $input[1]['created_by'] = 1;
        $input[1]['created_at'] = now();
        $input[1]['updated_at'] = now();

        $input[2]['name'] = 'Dystar';
        $input[2]['created_by'] = 1;
        $input[2]['created_at'] = now();
        $input[2]['updated_at'] = now();

        $input[3]['name'] = 'Gresik';
        $input[3]['created_by'] = 1;
        $input[3]['created_at'] = now();
        $input[3]['updated_at'] = now();

        $input[4]['name'] = 'Lokal';
        $input[4]['created_by'] = 1;
        $input[4]['created_at'] = now();
        $input[4]['updated_at'] = now();

        Supplier::insert($input);
    }
}
