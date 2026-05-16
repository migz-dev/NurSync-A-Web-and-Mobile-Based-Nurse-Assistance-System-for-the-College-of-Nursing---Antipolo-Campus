<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_ward_to_emergency_protocols.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('emergency_protocols', function (Blueprint $table) {
            $table->string('ward')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('emergency_protocols', function (Blueprint $table) {
            $table->dropColumn('ward');
        });
    }
};


