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
        Schema::create('ocorrencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')->constrained('funcionarios')->cascadeOnDelete();
            $table->string('tipo_ocorrencia')->nullable();
            $table->string('inicio_origem')->nullable();
            $table->string('fim_origem')->nullable();
            $table->string('dt_referencia')->nullable();
            $table->string('duracao_hhmm')->nullable();
            $table->string('atitude')->nullable();
            $table->string('modificador_por')->nullable();
            $table->string('total_geral')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ocorrencias');
    }
};
