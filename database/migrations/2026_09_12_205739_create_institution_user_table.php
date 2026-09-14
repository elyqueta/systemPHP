<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['user_id', 'institution_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_user');
    }
};
