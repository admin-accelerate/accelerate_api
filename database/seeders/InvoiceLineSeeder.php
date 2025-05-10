<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InvoiceLineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer 10 lignes de facture fictives
        InvoiceLine::factory()
            ->count(5)
            ->create([
                'invoice_id' => Invoice::factory()->create()->id,
            ]);
    }
}
