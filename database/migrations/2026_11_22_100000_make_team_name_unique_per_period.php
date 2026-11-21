<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Un club peut exister dans plusieurs périodes sous le même nom : l'unicité devient (période, nom). */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->unique(['period', 'name']);
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropUnique(['period', 'name']);
            $table->unique(['name']);
        });
    }
};
