<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentmethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_methods')->insert(["slug" => "cod", "title" => "COD"]);
        DB::table('payment_methods')->insert(["slug" => "stripe", "title" => "Credit/Debit Card"]);
    }
}
