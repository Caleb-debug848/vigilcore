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
        if (Schema::hasTable('incidents') && !Schema::hasColumn('incidents', 'statuspage_incident_id')) {
            Schema::table('incidents', function (Blueprint $table) {
                $table->string('statuspage_incident_id')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('incidents') && Schema::hasColumn('incidents', 'statuspage_incident_id')) {
            Schema::table('incidents', function (Blueprint $table) {
                $table->dropColumn('statuspage_incident_id');
            });
        }
    }


};
