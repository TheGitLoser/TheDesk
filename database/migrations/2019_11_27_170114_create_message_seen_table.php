<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageSeenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_seen', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chatroom_id');
            $table->integer('message_id');
            $table->integer('user_id');
            $table->string('seen_status', 2)->default('6');

            $table->timestamp('create_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_seen');
    }
}
