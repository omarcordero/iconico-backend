<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->double('subtotal', 8,2);
            $table->double('taxes', 8,2);
            $table->double('delivery_fee', 8,2);
            $table->double('total', 8,2);
            $table->double('discount', 8,2);
            $table->enum('status',
                    ['new','pending', 'cancelled', 'accepted','rejected', 'preparing', 'dispatched', 'intransit', 'complete']
            )->default('new');
            $table->enum('delivery_status',
                ['pending', 'allotted', 'started', 'cancelled', 'complete']
            )->default('pending');
            $table->enum('payment_status',
                ['unpaid', 'paid']
            )->default('unpaid');
            $table->integer('payment_method_id')->unsigned();
            $table->string('special_instructions', 255)->nullable();
            $table->integer('address_id')->unsigned();
            $table->integer('store_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('delivery_profile_id')->unsigned()->nullable(); // delivery boy id
            $table->timestamps();

            $table->foreign('payment_method_id', 'foreign_orders_payment_method')
                ->references('id')
                ->on('payment_methods')
                ->onDelete('cascade');
            $table->foreign('store_id', 'foreign_orders_store')
                ->references('id')
                ->on('stores')
                ->onDelete('cascade');
            $table->foreign('user_id', 'foreign_orders_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('delivery_profile_id', 'foreign_orders_delivery')
                ->references('id')
                ->on('delivery_profiles')
                ->onDelete('cascade');
            $table->foreign('address_id', 'foreign_orders_address')
                ->references('id')
                ->on('addresses')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
