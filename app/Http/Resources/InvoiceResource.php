<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\ClientResource;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'client' => new ClientResource($this->whenLoaded('client')),
            'invoice_number' => $this->invoice_number,
            'total_ht' => (float) $this->total_ht,
            'issue_date' => $this->formatDate($this->issue_date),
            'due_date' => $this->formatDate($this->due_date),
            'status' => $this->status,
            'lines' => InvoiceLineResource::collection($this->whenLoaded('lines')),
        ];
    }

    protected function formatDate($date)
    {
        // Si la date est déjà un objet Carbon, utilise toDateString directement
        if ($date instanceof Carbon) {
            return $date->toDateString();
        }

        // Si c'est une chaîne, convertir en Carbon
        return Carbon::parse($date)->toDateString();
    }
}
