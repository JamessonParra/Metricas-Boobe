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
        if (!Schema::hasTable('metric_history_runs')) {
            Schema::create('metric_history_runs', function (Blueprint $table) {
                $table->id();
                $table->string('url');
                $table->decimal('accessibility_metric', 5, 2);
                $table->decimal('pwa_metric', 5, 2);
                $table->decimal('performance_metric', 5, 2);
                $table->decimal('seo_metric', 5, 2);
                $table->decimal('best_practices_metric', 5, 2);
                $table->unsignedBigInteger('strategy_id')->nullable(); // Elimina "after best_practices_metric"
                $table->timestamps();

                // Agregar la relación de clave foránea
                $table->foreign('strategy_id')
                    ->references('id')
                    ->on('strategies')
                    ->onDelete('set null'); // Si se elimina una estrategia, se pone null
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metric_history_runs');
    }
};
