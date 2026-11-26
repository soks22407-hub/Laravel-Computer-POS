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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->decimal('subtotal');
            $table->decimal('total');
            $table->decimal('shipping');
            $table->string('name');
            $table->string('phone');
            $table->string('locality');
            $table->text('address');
            $table->string('province');
            $table->string('country');
            $table->string('landmark')->nullable();
            $table->string('type')->default('home');
            $table->enum('status', [
                'ordered',
                'shipped',
                'out_for_delivery',
                'delivered',
                'canceled'
            ])->default('ordered');
            $table->date('shipped_date')->nullable();
            $table->date('out_for_delivery_date')->nullable();
            $table->date('delivered_date')->nullable();
            $table->date('canceled_date')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
