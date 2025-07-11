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
        Schema::create('monthly_monitoring_reports', function (Blueprint $table) {
            $table->id();
            $table->string('monitor_name');
            $table->enum('monitoring_period', [
                'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
            ]);
            $table->foreignId('friendship_id')->constrained('friendships')->onDelete('cascade');
            $table->enum('general_evaluation', ['excelente', 'buena', 'regular', 'deficiente', 'critica']);
            $table->enum('meeting_frequency', ['semanal', 'quincenal', 'mensual', 'irregular']);
            $table->json('progress_areas')->nullable();
            $table->json('challenges')->nullable();
            $table->enum('tutor_participation', ['muy-activo', 'activo', 'moderado', 'pasivo', 'muy-pasivo']);
            $table->enum('leader_participation', ['muy-activo', 'activo', 'moderado', 'pasivo', 'muy-pasivo']);
            $table->enum('tutor_satisfaction', ['muy-satisfecho', 'satisfecho', 'neutral', 'insatisfecho', 'muy-insatisfecho']);
            $table->enum('leader_satisfaction', ['muy-satisfecho', 'satisfecho', 'neutral', 'insatisfecho', 'muy-insatisfecho']);
            $table->json('suggested_actions')->nullable();
            $table->enum('requires_attention', ['si', 'no']);
            $table->text('specific_observations')->nullable();
            $table->timestamps();

            // Índices para mejorar el rendimiento
            $table->index(['friendship_id', 'monitoring_period']);
            $table->index('general_evaluation');
            $table->index('requires_attention');
            $table->index('monitoring_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_monitoring_reports');
    }
};