<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->foreignId("plan_id")->constrained("plans")->onDelete("cascade");
            $table->string("transaction_id")->nullable();
            $table->string("payment_status")->nullable();
            $table->string("payment_response")->nullable();
            $table->string("payment_receipt")->nullable();
            $table->timestamp("start_date")->nullable();
            $table->timestamp("end_date")->nullable();
            $table->boolean("is_trial")->default(false);
            $table->boolean("is_active")->default(false);
            $table->boolean("is_cancelled")->default(false);
            $table->boolean("is_expired")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
};
