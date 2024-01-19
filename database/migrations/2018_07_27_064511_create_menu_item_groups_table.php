<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuItemGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_item_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->unsignedInteger('max_choices'); // maximum number of choices user can pick
            $table->unsignedInteger('min_choices'); // minimum number of choices user should pick
            $table->integer('menu_item_id')->unsigned();
            $table->timestamps();

            $table->foreign('menu_item_id', 'foreign_group_menu_item')
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
        Schema::table('menu_item_groups', function (Blueprint $table) {
            $table->dropForeign('foreign_group_menu_item');
        });

        Schema::dropIfExists('menu_item_groups');
    }
}
