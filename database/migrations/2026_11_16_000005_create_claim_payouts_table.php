<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warranty_claim_id')->unique()->constrained()->onDelete('cascade');
            $table->decimal('approved_amount', 12, 2)->unsigned();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_payouts');
    }
};
