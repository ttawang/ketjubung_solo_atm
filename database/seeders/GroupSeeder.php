<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
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
                'id' => 1,
                'name' => 'Group 1',
                'created_by' => 1
            ],
            [
                'id' => 2,
                'name' => 'Group 2',
                'created_by' => 1
            ],
            [
                'id' => 3,
                'name' => 'Group 3',
                'created_by' => 1
            ],
        ];
        DB::table('tbl_group')->insert($data);
    }
}
