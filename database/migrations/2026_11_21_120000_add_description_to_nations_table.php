<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Description libre d'une nation (éditée depuis la base de données, comme celle d'une équipe). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nations', function (Blueprint $table) {
            $table->text('description')->nullable()->after('management_philosophy');
        });
    }

    public function down(): void
    {
        Schema::table('nations', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
