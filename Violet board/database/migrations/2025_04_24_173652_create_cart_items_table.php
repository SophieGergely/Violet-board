<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->smallInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['cart_id', 'product_id']);
        });

        DB::statement("
            ALTER TABLE cart_items
                ADD CONSTRAINT chk_cart_item_quantity
                    CHECK (quantity > 0)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
