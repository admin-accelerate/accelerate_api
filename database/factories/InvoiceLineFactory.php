<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceLine>
 */
class InvoiceLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => null,
            'description' => $this->faker->sentence,
            'quantity' => $this->faker->numberBetween(1, 10),
            'unit_price' => $this->faker->randomFloat(2, 10, 1000), // Prix unitaire entre 10 et 1000

            /*'total_ht' => function (array $attributes) {
                return $attributes['quantity'] * $attributes['unit_price'];
            },
            'total_ttc' => function (array $attributes) {
                return $attributes['total_ht'] * 1.2; // Ajout de 20% de TVA
            },
            'tax_rate' => 20, // Taux de TVA
            'tax_amount' => function (array $attributes) {
                return $attributes['total_ht'] * ($attributes['tax_rate'] / 100);
            },*/
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
