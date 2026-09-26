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
        if (!Schema::hasTable('module_features')) {
            Schema::create('module_features', function (Blueprint $table) {
                $table->id();
                $table->string('module_code', 50)->unique();
                $table->string('module_name', 100);
                $table->string('category', 50)->default('Core');
                $table->boolean('is_enabled')->default(false);
                $table->json('config')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_features');
    }
};
