<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('source'); // e.g. refer, withdraw_to_bank, order etc
            $table->string('image_url')->nullable();
            $table->integer('order_id')->unsigned()->nullable();

            $table->foreign('order_id', 'transactions_foreign_order')
                ->references('id')
                ->on('orders')
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
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('source');
            $table->dropColumn('image_url');
        });
    }
}
