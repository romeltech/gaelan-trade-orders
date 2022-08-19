<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable();
            $table->string('item_name')->nullable();
            $table->smallInteger('non_foc_quantity')->nullable();
            $table->smallInteger('foc_quantity')->nullable();
            $table->smallInteger('total_quantity')->nullable();
            $table->string('uom')->nullable();
            $table->decimal('price', 8,2)->nullable();
            $table->decimal('line_price', 8,2)->nullable();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('item_id')->constrained();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('order_details');
    }
}
