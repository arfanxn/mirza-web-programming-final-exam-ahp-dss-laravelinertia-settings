<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserFactory::new()->create([
            'name' => 'Arfan',
            'email' => 'arfan@ahp-dss.com',
            'password' => bcrypt('11112222'),
        ]);

        UserFactory::new()->create([
            'name' => 'Matt',
            'email' => 'matt@ahp-dss.com',
            'password' => bcrypt('11112222'),
        ]);
    }
}
