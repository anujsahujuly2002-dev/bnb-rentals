<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('traveler_payment_transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('traveler_id')->unsigned()->nullable();
            $table->foreign('traveler_id')->references('id')->on('traveller_information')->onDelete('cascade');
            $table->float('total_amount')->nullable();
            $table->enum('type',['0','1','2'])->comment('0=stripe,1=paypal,2=authorizenet')->default('0');
            $table->enum('payment_status',['pending','success'])->default('pending');
            $table->json('payment_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traveler_payment_transaction_histories');
    }
};
