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
        Schema::table('incidents', function (Blueprint $table) {
            if (!Schema::hasColumn('incidents', 'component')) {
                $table->string('component')->nullable()->after('source');
            }
            if (!Schema::hasColumn('incidents', 'alert_name')) {
                $table->string('alert_name')->nullable()->after('component');
            }
            if (!Schema::hasColumn('incidents', 'message')) {
                $table->text('message')->nullable()->after('description');
            }
            if (!Schema::hasColumn('incidents', 'server')) {
                $table->string('server')->nullable()->default('srv901529')->after('message');
            }
            if (!Schema::hasColumn('incidents', 'is_resolved')) {
                $table->boolean('is_resolved')->default(false)->after('status');
            }
            if (!Schema::hasColumn('incidents', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('is_resolved');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn([
                'component',
                'alert_name',
                'message',
                'server',
                'is_resolved',
                'resolved_at',
            ]);
        });
    }
};
