<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->onDelete('cascade');
            $table->string('session_id', 255)->nullable()->index();
            $table->timestamps();
        });

        DB::statement("
            ALTER TABLE carts
                ADD CONSTRAINT chk_cart_owner
                    CHECK (user_id IS NOT NULL OR session_id IS NOT NULL)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
