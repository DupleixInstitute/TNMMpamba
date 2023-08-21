<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loan_product_scoring_attribute_option_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('loan_product_scoring_attribute_id')->nullable();
            $table->unsignedBigInteger('scoring_attribute_id')->nullable();
            $table->decimal('weight')->default(0.0);
            $table->decimal('effective_weight')->default(0.0);
            $table->decimal('score')->default(0.0);
            $table->decimal('weighted_score')->default(0.0);
            $table->text('name')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_product_scoring_attribute_option_values');
    }
};
