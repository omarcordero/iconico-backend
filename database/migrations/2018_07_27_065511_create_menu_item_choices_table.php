<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuItemChoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_item_choices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->double('price', 8,2);
            $table->integer('menu_item_group_id')->unsigned();
            $table->timestamps();

            $table->foreign('menu_item_group_id', 'foreign_choice_group')
                ->references('id')
                ->on('menu_item_groups')
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
        Schema::table('menu_item_choices', function (Blueprint $table) {
            $table->dropForeign('foreign_choice_group');
        });

        Schema::dropIfExists('menu_item_choices');
    }
}
