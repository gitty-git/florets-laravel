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
            $table->timestamps();
            $table->enum('status', ['created', 'processed', 'sent', 'received', 'canceled'])->default('created');
            $table->boolean('paid')->default(0);
            $table->string('name');
            $table->string('comment')->nullable();
            $table->string('delivery_time');
            $table->string('phone');
            $table->string('address');
            $table->json('cart');
            $table->enum('payment_method', ['card', 'cash']);
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
