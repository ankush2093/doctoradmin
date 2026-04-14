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
        if (!Schema::hasTable('doctor_receipts')) {
            return;
        }

        Schema::table('doctor_receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('doctor_receipts', 'receipt_url')) {
                $table->text('receipt_url')->nullable()->after('receipt');
            }

            if (!Schema::hasColumn('doctor_receipts', 'receipt_no')) {
                $table->string('receipt_no')->nullable()->after('receipt_url');
            }

            if (!Schema::hasColumn('doctor_receipts', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('receipt_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('doctor_receipts')) {
            return;
        }

        Schema::table('doctor_receipts', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('doctor_receipts', 'receipt_url')) {
                $columnsToDrop[] = 'receipt_url';
            }

            if (Schema::hasColumn('doctor_receipts', 'receipt_no')) {
                $columnsToDrop[] = 'receipt_no';
            }

            if (Schema::hasColumn('doctor_receipts', 'created_by')) {
                $columnsToDrop[] = 'created_by';
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
