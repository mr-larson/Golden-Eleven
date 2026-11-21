<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Période de jeu à laquelle appartient chaque club de référence (college, pro…). */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('period', 20)->default('college')->after('name')->index();
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex(['period']);
            $table->dropColumn('period');
        });
    }
};
