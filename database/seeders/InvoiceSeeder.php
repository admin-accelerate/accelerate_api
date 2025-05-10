<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run()
    {
        // Vider les tables pour éviter les conflits (optionnel, à décommenter si nécessaire)
        // Invoice::truncate();
        // InvoiceLine::truncate();

        // Créer 5 clients fictifs (si nécessaire)
        $clients = Client::factory()->count(5)->create();

        // Récupérer le dernier numéro de facture existant
        $lastInvoice = Invoice::orderBy('invoice_number', 'desc')->first();
        $nextIndex = $lastInvoice ? (int)substr($lastInvoice->invoice_number, -4) + 1 : 1;

        // Créer 10 factures
        $invoices = Invoice::factory()
            ->count(10)
            ->make()
            ->each(function ($invoice, $index) use ($clients, $nextIndex) {
                // Assigner un client aléatoire
                $invoice->client_id = $clients->random()->id;
                // Générer un numéro de facture unique basé sur le dernier index
                $invoice->invoice_number = 'INV-' . Carbon::now()->year . '-' . str_pad($nextIndex + $index, 4, '0', STR_PAD_LEFT);
                // Définir des dates cohérentes
                $invoice->issue_date = Carbon::now()->subDays(rand(0, 30));
                $invoice->due_date = $invoice->issue_date->copy()->addDays(rand(7, 30));
                // Statut aléatoire
                $invoice->status = collect(['draft', 'paid', 'cancelled'])->random();
                // Sauvegarder la facture
                $invoice->save();

                // Créer 2 à 5 lignes de facture par facture
                $lineCount = rand(2, 5);
                InvoiceLine::factory()->count($lineCount)->create([
                    'invoice_id' => $invoice->id,
                ]);
            });

        // Optionnel : Vérifier que total_ht est bien recalculé
        foreach ($invoices as $invoice) {
            $invoice->refresh(); // Recharge pour refléter total_ht recalculé
        }
    }

    /*public function run()
    {
        // Créer 5 clients fictifs (si nécessaire)
        $clients = Client::factory()->count(5)->create();

        // Créer 10 factures
        $invoices = Invoice::factory()
            ->count(10)
            ->make()
            ->each(function ($invoice, $index) use ($clients) {
                // Assigner un client aléatoire
                $invoice->client_id = $clients->random()->id;
                // Générer un numéro de facture unique
                $invoice->invoice_number = 'INV-' . Carbon::now()->year . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
                // Définir des dates cohérentes
                $invoice->issue_date = Carbon::now()->subDays(rand(0, 30));
                $invoice->due_date = $invoice->issue_date->copy()->addDays(rand(7, 30));
                // Statut aléatoire
                $invoice->status = collect(['draft', 'paid', 'cancelled'])->random();
                // Sauvegarder la facture
                $invoice->save();

                // Créer 2 à 5 lignes de facture par facture
                $lineCount = rand(2, 5);
                InvoiceLine::factory()->count($lineCount)->create([
                    'invoice_id' => $invoice->id,
                ]);
            });

        // Optionnel : Vérifier que total_ht est bien recalculé
        foreach ($invoices as $invoice) {
            $invoice->refresh(); // Recharge pour refléter total_ht recalculé
        }
    }*/
}
