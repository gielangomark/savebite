<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            if (!Schema::hasColumn('merchants', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('merchants', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            if (Schema::hasColumn('merchants', 'latitude')) {
                $table->dropColumn('latitude');
            }
            if (Schema::hasColumn('merchants', 'longitude')) {
                $table->dropColumn('longitude');
            }
        });
    }
};
