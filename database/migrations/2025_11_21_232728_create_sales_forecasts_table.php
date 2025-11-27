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
        Schema::create('sales_forecasts', function (Blueprint $table) {
            $table->id();
            $table->date('forecast_date');
            $table->enum('period_type', ['daily', 'weekly', 'monthly']);
            $table->decimal('predicted_revenue', 15, 2);
            $table->decimal('predicted_transactions', 10, 2);
            $table->decimal('lower_bound', 15, 2)->nullable();
            $table->decimal('upper_bound', 15, 2)->nullable();
            $table->decimal('confidence_score', 5, 4)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['forecast_date', 'period_type']);
            $table->index(['period_type', 'forecast_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_forecasts');
    }
};
