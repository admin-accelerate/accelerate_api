<?php

namespace App\Models;

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

    // Recalculer total_ht à chaque sauvegarde
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($invoice) {
            $invoice->total_ht = $invoice->lines->sum('total_amount');
        });
    }
}
