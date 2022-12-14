<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('draft'); // draft, confirmed
            $table->boolean('erp')->default(false);
            $table->boolean('is_cash_sale')->default(false);
            $table->string('cash_sale_customer')->nullable();
            $table->string('order_number')->nullable();
            $table->foreignId('location_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained();
            $table->text('instructions')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
