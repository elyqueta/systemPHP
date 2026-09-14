<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_tax_configurations', function (Blueprint $table) {
            $table->foreignId('institution_id')
                ->primary()
                ->constrained('institutions')
                ->cascadeOnDelete();

            $table->decimal('employee_social_security_rate', 5, 2)->default(3.00);
            $table->decimal('employer_social_security_rate', 5, 2)->default(8.00);
            $table->decimal('meal_allowance', 12, 2)->default(15000.00);
            $table->decimal('transport_allowance', 12, 2)->default(10000.00);
            $table->unsignedInteger('currency_id')->nullable();
            $table->string('tax_regime', 50)->default('Geral');

            $table->timestamp('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_tax_configurations');
    }
};
