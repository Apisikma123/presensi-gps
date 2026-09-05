<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            Cabangpermissionseeder::class,
            Hariliburpermissionseeder::class,
            Izinabsenpermissionseeder::class,
            Izincutipermissionseeder::class,
            Izinsakitpermissionseeder::class,
            Jabatanpermissionseeder::class,
            Jamkerjapermissionseeder::class,
            Laporanpermissionseeder::class,
            LaporanCutiPermissionSeeder::class,
            Pengaturanumumpermissionseeder::class,
            Presensipermissionseeder::class,
            Trackingpresensipermissionseeder::class,
            MasterAdminSeeder::class,
        ]);
    }
}
