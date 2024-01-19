<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('earnings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->double('amount', 8,2);
            $table->integer('user_id')->unsigned();
            $table->boolean('paid')->default(false);
            $table->timestamps();

            $table->foreign('order_id', 'earnings_foreign_order')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');

            $table->foreign('user_id', 'earnings_foreign_user')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('earnings');
    }
}
