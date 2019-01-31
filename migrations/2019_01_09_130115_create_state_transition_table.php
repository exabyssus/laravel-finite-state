<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStateTransitionTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('state_transitions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('owner_class');
            $table->integer('owner_id');
            $table->string('transition');
            $table->string('to');
            $table->integer('user_id')->nullable();
            $table->timestamps();

            $table->index(['owner_class', 'owner_id']);
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_states');
    }
}