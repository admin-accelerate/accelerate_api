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
        Schema::table('invoice_lines', function (Blueprint $table) {
            // Supprimer la colonne amount
            $table->dropColumn('amount');
            // Ajouter les nouvelles colonnes
            $table->unsignedInteger('quantity')->after('description')->default(1);
            $table->decimal('unit_price', 8, 2)->after('quantity');
            $table->decimal('total_amount', 8, 2)->after('unit_price')->storedAs('quantity * unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'unit_price', 'total_amount']);
            $table->decimal('amount', 8, 2)->after('description');
        });
    }
};
