<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_plan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id', 50);
            $table->string('company_name', 50);
            $table->mediumText('profile')->nullable();
            $table->string('profile_picture', 500)->nullable();  //URL

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
        Schema::dropIfExists('business_plan');
    }
}
