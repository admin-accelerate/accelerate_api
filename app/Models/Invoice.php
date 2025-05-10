<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Client;
use App\Models\InvoiceLine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['client_id', 'invoice_number', 'total_ht', 'issue_date', 'due_date', 'status'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lines()
    {
        return $this->hasMany(InvoiceLine::class);
    }

    // Pour calculer le total HT de la facture  on utilise la méthode saving() pour mettre à jour dans la base de données
    // On utilise la méthode sum() pour additionner les montants de chaque ligne de la facture
    // On utilise la méthode creating() pour générer un numéro de facture unique
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($invoice) {
            $invoice->total_ht = $invoice->lines()->sum('total_amount') ?? 0;
        });

        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = static::generateUniqueInvoiceNumber();
            }
        });
    }
    //Pour générer un numéro de facture unique
    // en fonction de l'année et du dernier numéro de facture
    // Exemple : INV-2025-0001
    public static function generateUniqueInvoiceNumber()
    {
        $year = Carbon::now()->year;
        $lastNumber = static::max('id') ?? 0;
        return 'INV-' . $year . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
