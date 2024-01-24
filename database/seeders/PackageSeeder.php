<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       /*  Package::create([
            'package_name'=>'Bronze',
            'package_price'=>199
        ]);
        Package::create([
            'package_name'=>'Silver',
            'package_price'=>299
        ]); */
        Package::create([
            'package_name'=>'Platinum',
            'package_price'=>399
        ]);
    }
}
