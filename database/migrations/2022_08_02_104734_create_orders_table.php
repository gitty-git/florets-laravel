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
        Schema::create('orders', function (Blueprint $table) {
            $table->string('id')->primary();            
            $table->boolean('viewed')->default(false);
            $table->timestamps();
            $table->enum('status', ['created', 'processed', 'sent', 'received', 'canceled'])->default('created');
            $table->boolean('paid')->default(0);            
            $table->string('name');
            $table->string('comment')->nullable();
            $table->string('delivery_time');
            $table->string('phone');
            $table->string('address');
            $table->json('cart');
            $table->enum('payment_method', ['card', 'cash', 'online']);
            $table->enum('delivery_method', ['me', 'another', 'self_delivery'])->default('me');
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('apt')->nullable();
            $table->string('paymentId')->nullable();
            $table->integer('amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
