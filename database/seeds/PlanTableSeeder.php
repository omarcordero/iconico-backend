<?php

use Illuminate\Database\Seeder;
use Rennokki\Plans\Models\PlanModel;

class PlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $premium = PlanModel::create([
            'name' => 'Super Saver',
            'description' => 'Super Saver',
            'price' => 1200.0,
            'currency' => env('APP_CURRENCY', 'INR'),
            'duration' => 365, // in days
        ]);

        $economy = PlanModel::create([
            'name' => 'Popular',
            'description' => 'Popular',
            'price' => 450.0,
            'currency' => env('APP_CURRENCY', 'INR'),
            'duration' => 90, // in days
        ]);

        $basic = PlanModel::create([
            'name' => 'Standard',
            'description' => 'Standard',
            'price' => 200.0,
            'currency' => env('APP_CURRENCY', 'INR'),
            'duration' => 30, // in days
        ]);
    }
}
