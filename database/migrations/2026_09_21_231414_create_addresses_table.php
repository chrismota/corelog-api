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
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('customer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('zip_code', 9);
            $table->string('street', 150);
            $table->string('number', 20);
            $table->string('complement', 100)->nullable();
            $table->string('neighborhood', 100);
            $table->string('city', 100);
            $table->string('state', 2);

            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
