<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appliances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->string('appliance_type', 100);
            $table->string('model_number', 100);
            $table->string('serial_number', 100)->unique();
            $table->date('purchase_date');
            $table->timestamps();

            $table->index(['customer_id', 'appliance_type']);
            $table->index('purchase_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appliances');
    }
};
