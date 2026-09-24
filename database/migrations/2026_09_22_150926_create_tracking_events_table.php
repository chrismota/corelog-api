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
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('shipment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('status');

            $table->string('description');

            $table->string('location')->nullable();

            $table->timestamp('occurred_at');

            $table->timestamps();

            $table->index(['shipment_id', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
    }
};
