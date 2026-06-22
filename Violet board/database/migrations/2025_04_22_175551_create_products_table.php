<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('label_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('discount_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->smallInteger('min_age');
            $table->smallInteger('min_players');
            $table->smallInteger('max_players');
            $table->boolean('in_stock')->default(true);
            $table->smallInteger('play_time_min')->nullable();
            $table->smallInteger('play_time_max')->nullable();
            $table->decimal('bgg_rating', 4, 2)->nullable();
            $table->decimal('weight', 3, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("
            ALTER TABLE products
                ADD CONSTRAINT chk_product_price
                    CHECK (price >= 0),
                ADD CONSTRAINT chk_product_min_age
                    CHECK (min_age >= 0),
                ADD CONSTRAINT chk_product_min_players
                    CHECK (min_players >= 1),
                ADD CONSTRAINT chk_product_max_players
                    CHECK (max_players >= min_players),
                ADD CONSTRAINT chk_product_play_time_min
                    CHECK (play_time_min IS NULL OR play_time_min >= 1),
                ADD CONSTRAINT chk_product_play_time_max
                    CHECK (play_time_max IS NULL OR play_time_max >= play_time_min),
                ADD CONSTRAINT chk_product_bgg_rating
                    CHECK (bgg_rating IS NULL OR (bgg_rating >= 1 AND bgg_rating <= 10)),
                ADD CONSTRAINT chk_product_weight
                    CHECK (weight IS NULL OR (weight >= 1 AND weight <= 5))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
