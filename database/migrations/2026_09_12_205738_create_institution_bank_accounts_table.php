<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('institution_id')
                ->constrained('institutions')
                ->cascadeOnDelete();

            $table->unsignedInteger('bank_id')->nullable();
            $table->string('bank_name', 150)->nullable();
            $table->string('account_number', 50);
            $table->string('iban', 34)->nullable();
            $table->unsignedInteger('currency_id')->nullable();
            $table->boolean('is_primary')->default(false);

            $table->timestamp('created_at')->useCurrent();

            $table->index('institution_id');
        });

        DB::statement('
            CREATE UNIQUE INDEX idx_one_primary_account_per_institution
            ON institution_bank_accounts (institution_id)
            WHERE is_primary = TRUE
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_bank_accounts');
    }
};
