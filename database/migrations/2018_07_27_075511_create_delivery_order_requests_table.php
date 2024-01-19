<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryOrderRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_order_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('delivery_profile_id')->unsigned();
            $table->integer('order_id')->unsigned();
            $table->enum('status', ['pending', 'accepted','rejected'])->default('pending');
            $table->timestamps();

            /*
             * Add Foreign/Unique/Index
             */
            $table->foreign('delivery_profile_id', 'order_request_foreign_delivery_profile')
                ->references('id')
                ->on('delivery_profiles')
                ->onDelete('cascade');

            $table->foreign('order_id', 'order_request_foreign_order')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');

            $table->unique(['delivery_profile_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_order_requests', function (Blueprint $table) {
            $table->dropForeign('order_request_foreign_delivery_profile');
            $table->dropForeign('order_request_foreign_order');
        });

        /*
         * Drop tables
         */
        Schema::dropIfExists('delivery_order_requests');
    }
}
