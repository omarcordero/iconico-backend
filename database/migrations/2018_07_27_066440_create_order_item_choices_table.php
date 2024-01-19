<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemChoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_item_choices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->integer('menu_item_id')->unsigned();
            $table->integer('menu_item_choice_id')->unsigned();
            $table->double('total', 8,2);
            $table->timestamps();

            $table->foreign('order_id', 'order_item_choices_foreign_order')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');
            $table->foreign('menu_item_id', 'order_item_choices_foreign_menu_item')
                ->references('id')
                ->on('menu_items')
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
        Schema::dropIfExists('order_item_choices');
    }
}
