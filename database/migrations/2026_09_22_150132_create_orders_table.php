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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('organization_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('customer_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('order_number');

            $table->string('status')->default('PENDING');

            $table->string('shipping_name');
            $table->string('shipping_zip_code');
            $table->string('shipping_street');
            $table->string('shipping_number');
            $table->string('shipping_complement')->nullable();
            $table->string('shipping_neighborhood');
            $table->string('shipping_city');
            $table->string('shipping_state', 2);

            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);

            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
