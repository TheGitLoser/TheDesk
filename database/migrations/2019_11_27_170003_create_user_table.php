<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');   // integer
            $table->string('unique_id', 50);
            $table->string('name', 50);
            $table->string('display_id', 50);
            $table->string('email', 80);
            $table->string('password', 80);
            $table->string('type', 20);
            $table->string('phone', 10)->nullable();
            $table->timestamp('DOB')->default(\DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->mediumText('profile')->nullable();
            $table->string('profile_picture', 500)->nullable();  // URL
            
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
        Schema::dropIfExists('user');
    }
}
