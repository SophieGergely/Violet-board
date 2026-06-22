<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->decimal('value', 5, 2);
            $table->timestampTz('starts_at')->nullable();
            $table->timestampTz('ends_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::statement("
            ALTER TABLE discounts
                ADD CONSTRAINT chk_discount_type
                    CHECK (type IN ('percentage', 'fixed')),
                ADD CONSTRAINT chk_discount_value
                    CHECK (
                        (type = 'percentage' AND value > 0 AND value <= 100)
                        OR
                        (type = 'fixed' AND value > 0)
                    ),
                ADD CONSTRAINT chk_discount_dates
                    CHECK (ends_at IS NULL OR ends_at > starts_at)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
