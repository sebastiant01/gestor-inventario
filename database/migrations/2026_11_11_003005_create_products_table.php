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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name', 100);
            $table->decimal('product_price', 10, 2);
            $table->string('product_description', 200)->nullable();
            $table->integer('available_units');
            $table->boolean('is_available')->default(true);

            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('user_id');

            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('category_id')->references('category_id')->on('categories');
            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers');
            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
