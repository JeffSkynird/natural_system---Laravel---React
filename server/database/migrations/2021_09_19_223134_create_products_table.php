<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('bar_code');
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->double('stock', 8, 3);
            $table->double('min_stock', 8, 3)->nullable();
            $table->double('max_stock', 8, 3)->nullable();
            $table->integer('has_iva')->default(1);
            $table->integer('fraction');
            $table->double('list_price', 8, 3)->default(0);
            $table->double('sale_price', 8, 3)->default(0);
            $table->foreignId('unity_id')->constrained('unities');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses');
            $table->boolean('status')->default(1);
            $table->string('ip')->nullable();
            $table->string('terminal')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
