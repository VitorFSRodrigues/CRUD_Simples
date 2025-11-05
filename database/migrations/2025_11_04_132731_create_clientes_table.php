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
       Schema::create('clientes', function (Blueprint $table) {
            $table->id(); // ID PK
            $table->string('nome_cliente', 255);
            $table->string('endereco_completo', 255);
            $table->string('municipio', 255);
            $table->string('estado', 255);
            $table->string('pais', 255);
            $table->string('cnpj', 255);
            $table->softDeletes(); // Deleted_at
            $table->timestamps();  // Created_at / Updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
