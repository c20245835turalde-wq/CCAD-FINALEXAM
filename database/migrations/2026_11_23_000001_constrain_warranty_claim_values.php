<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        if (DB::table('warranty_claims')->whereNotIn('claim_status', ['pending', 'approved', 'rejected'])->exists()) {
            throw new RuntimeException('Cannot constrain claim statuses while unsupported values exist.');
        }

        if (DB::table('claim_payouts')->where('approved_amount', '<', 0)->exists()) {
            throw new RuntimeException('Cannot constrain payout amounts while negative values exist.');
        }

        DB::statement("ALTER TABLE warranty_claims MODIFY claim_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
        DB::statement('ALTER TABLE claim_payouts MODIFY approved_amount DECIMAL(12, 2) UNSIGNED NOT NULL');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE warranty_claims MODIFY claim_status VARCHAR(20) NOT NULL DEFAULT 'pending'");
        DB::statement('ALTER TABLE claim_payouts MODIFY approved_amount DECIMAL(12, 2) NOT NULL');
    }
};
