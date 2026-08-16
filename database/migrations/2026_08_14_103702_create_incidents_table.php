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
            $table->string('source');                                // Zabbix, Kibana Logs, n8n Hub
            $table->string('title');                                 // Intitulé de l'alerte
            $table->text('description')->nullable();                 // Description détaillée
            $table->string('severity')->default('info');             // critical, warning, info
            $table->string('status')->default('open');               // open, investigating, resolved
            $table->string('statuspage_incident_id')->nullable();    // ID Atlassian Statuspage
            $table->json('raw_payload')->nullable();                 // Données brutes reçues du webhook
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