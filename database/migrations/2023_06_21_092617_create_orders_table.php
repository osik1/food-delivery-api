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
            $table->uniqid('order_code');
            $table->bigInteger('restaurant_id')->unsigned()->nullable()->index();
            $table->bigInteger('menu_id')->unsigned()->nullable()->index();
            $table->bigInteger('user_id')->unsigned()->nullable()->index();
            $table->string('bowl_qty');
            $table->decimal('total_amount')->nullable();
            $table->tinyInteger('order_status')->default(0);
            $table->tinyInteger('receipt_status')->default(0);
            $table->tinyInteger('delete_status')->default(1);
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
