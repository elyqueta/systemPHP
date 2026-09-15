<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_irt_brackets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->date('effective_from');
            $table->unsignedTinyInteger('bracket_order');

            $table->decimal('lower_bound', 14, 2);
            $table->decimal('upper_bound', 14, 2)->nullable();

            $table->decimal('rate', 5, 2);
            $table->decimal('fixed_amount', 14, 2);

            $table->timestamps();

            $table->unique(['effective_from', 'bracket_order']);
            $table->index('effective_from');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_irt_brackets');
    }
};
