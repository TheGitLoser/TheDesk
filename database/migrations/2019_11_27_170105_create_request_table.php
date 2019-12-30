<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('business_plan_id'); 
            $table->mediumText('content')->nullable();

            $table->timestamp('create_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('update_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->string('status', 2)->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request');
    }
}
