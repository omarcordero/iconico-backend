<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('description');
            $table->enum('status', ['debit', 'credit'])->nullable();
            $table->double('amount')->nullable();
            $table->integer('user_id')->unsigned();
            $table->boolean('is_paid')->default(false);
            $table->timestamps();

            $table->foreign('user_id', 'transactions_foreign_user')
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
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('transactions_foreign_user');
        });

        Schema::dropIfExists('transactions');
    }
}
