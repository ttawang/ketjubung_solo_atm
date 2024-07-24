<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            GudangSeeder::class,
            RolesSeeder::class,
            UsersSeeder::class,
            MenusSeeder::class,
            MappingMenusSeeder::class,
            // SupplierSeeder::class,
            SatuanSeeder::class,
            TipeSeeder::class,
            // BarangSeeder::class,
            // WarnaSeeder::class,
            TipePengirimanSeeder::class,
            // MotifSeeder::class,
            GradeSeeder::class,
            KualitasSeeder::class,
            ProductionCodeSeeder::class,
            // MesinSeeder::class,
            // NomorBeamSeeder::class,
            GroupSeeder::class,
            // PekerjaSeeder::class,
            AbsensiShiftSeeder::class
        ]);
    }
}
