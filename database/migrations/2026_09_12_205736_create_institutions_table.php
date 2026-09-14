<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('name', 200);
            $table->string('commercial_name', 200)->nullable();
            $table->string('tax_id', 20)->unique()->nullable();
            $table->string('institution_type', 10)->default('LDA');

            $table->date('founding_date')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('website', 200)->nullable();

            $table->string('address', 250)->nullable();
            $table->string('neighborhood', 150)->nullable();
            $table->string('city', 100)->nullable();
            $table->unsignedInteger('province_id')->nullable();
            $table->unsignedInteger('municipality_id')->nullable();

            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->index('active');
        });

        DB::statement("
            ALTER TABLE institutions
            ADD CONSTRAINT chk_institutions_type
            CHECK (institution_type IN ('LDA', 'SA', 'ENI', 'ONG', 'EP', 'OUTRO'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
