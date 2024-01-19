<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            ["key" => "currency", "value" => "INR"],
            ["key" => "distance_metric", "value" => "KM"],
            ["key" => "delivery_fee", "value" => "25"],
            ["key" => "admin_fee_subscription_or_per_order", "value" => "per_order"], // possible values subscription, per_order
            ["key" => "admin_fee_for_order_in_percent", "value" => "10"],
            ["key" => "tax_in_percent", "value" => "10"],
            ["key" => "support_email", "value" => "admin@example.com"],
            ["key" => "support_phone", "value" => "8181818118"],
            ["key" => "send_welcome_email", "value" => "1"],
            ["key" => "send_order_placed_email", "value" => "1"],
            ["key" => "send_order_complete_email", "value" => "1"],
            ["key" => "cod_enabled", "value" => "1"],
            ["key" => "refer_amount", "value" => "50"],
            ["key" => "delivery_fee_set_by", "value" => "admin"], // possible values admin, store, distance
            ["key" => "delivery_fee_per_km_charge", "value" => "5"], // if delivery fee is of type distance, then this value will be used to calculate delivery fee for whole distance
        ]);
    }
}
