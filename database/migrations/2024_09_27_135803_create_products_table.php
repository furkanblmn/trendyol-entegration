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
            $table->id();
            $table->string('barcode');
            $table->integer('product_code')->index();
            $table->string('name')->index();
            $table->float('price')->default(123.45);
            $table->string('stock_unit_type');
            $table->integer('quantity');
            $table->longText('description');
            $table->tinyInteger('is_varianted')->default(0)->comment('1: varyantı var, 0: varyantı bulunmuyor.');
            $table->timestamps();
            $table->softDeletes();
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
