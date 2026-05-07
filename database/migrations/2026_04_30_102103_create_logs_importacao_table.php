<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs_importacao', function (Blueprint $table) {
            $table->id();
            $table->string('nome_arquivo');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('linhas_sucesso')->default(0);
            $table->integer('linhas_erro')->default(0);
            $table->json('detalhes_erros')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_importacao');
    }
};
