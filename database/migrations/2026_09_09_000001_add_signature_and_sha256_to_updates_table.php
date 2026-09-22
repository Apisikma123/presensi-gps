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
        Schema::table('updates', function (Blueprint $table) {
            if (!Schema::hasColumn('updates', 'sha256')) {
                $table->string('sha256', 64)->nullable()->after('checksum');
            }
            if (!Schema::hasColumn('updates', 'signature')) {
                $table->text('signature')->nullable()->after('sha256');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('updates', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('updates', 'sha256')) {
                $columns[] = 'sha256';
            }
            if (Schema::hasColumn('updates', 'signature')) {
                $columns[] = 'signature';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
