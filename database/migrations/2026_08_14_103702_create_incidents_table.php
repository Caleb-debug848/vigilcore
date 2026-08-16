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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('source'); // Source de la capture (ex: Zabbix, Kibana)
            $table->string('title'); // Intitulé de l'alerte ou de l'incident
            $table->text('description')->nullable(); // Description détaillée
            $table->string('severity')->default('info'); // Sévérité : critical, warning, info
            $table->string('status')->default('open'); // Statut interne / synchronisé avec Cachet
            $table->json('raw_payload')->nullable(); // Données brutes reçues par le webhook n8n
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};