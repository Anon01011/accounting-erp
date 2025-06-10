<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Create 10 sample customers
        for ($i = 0; $i < 10; $i++) {
            Customer::create([
                'name' => $faker->company,
                'type' => $faker->randomElement(['individual', 'company']),
                'email' => $faker->companyEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'status' => true,
            ]);
        }
    }
} 