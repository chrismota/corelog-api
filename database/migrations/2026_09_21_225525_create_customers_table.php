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
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('organization_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('name', 150);
            $table->string('document', 20);
            $table->string('email');
            $table->string('phone', 20);

            $table->softDeletes();

            $table->timestamps();

            $table->unique([
                'organization_id',
                'document',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
