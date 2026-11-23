<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranty_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appliance_id')->unique()->constrained()->onDelete('restrict');
            $table->date('warranty_expiry');
            $table->timestamps();

            $table->index('warranty_expiry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranty_policies');
    }
};
