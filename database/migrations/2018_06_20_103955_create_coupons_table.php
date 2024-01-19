<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 32)->unique();
            $table->integer('reward');
            $table->enum('type', ['fixed', 'percent']);
            $table->text('data')->nullable();
            $table->date('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('coupons_users', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('coupon_id');
            $table->timestamp('used_at');

            $table->primary(['user_id', 'coupon_id']);

            $table->foreign('user_id', 'foreign_coupons_users_user')->references('id')->on('users');
            $table->foreign('coupon_id', 'foreign_coupons_users_coupon')->references('id')->on('coupons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons_users', function (Blueprint $table) {
            $table->dropForeign('foreign_coupons_users_user');
            $table->dropForeign('foreign_coupons_users_coupon');
        });

        Schema::dropIfExists('coupons_users');

        Schema::dropIfExists('coupons');
    }
}
