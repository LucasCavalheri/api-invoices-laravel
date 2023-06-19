<?php

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{

    private array $types = ['C' => 'Cartão', 'B' => 'Boleto', 'P' => 'Pix'];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $value = number_format($this->value, 2, ',', '.');
        $paid = $this->paid;
        $paymentDate = $paid ? Carbon::parse($this->payment_date)->format('d/m/Y H:i:s') : null;
        $paymentSince = $paid ? Carbon::parse($this->payment_date)->diffForHumans() : null;

        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'firstName' => $this->user->firstName,
                'lastName' => $this->user->lastName,
                'fullName' => "{$this->user->firstName} {$this->user->lastName}",
                'email' => $this->user->email,
            ],
            'type' => $this->types[$this->type],
            'value' => "R$ {$value}",
            'paid' => $paid ? 'Pago' : 'Não Pago',
            'paymentDate' => $paymentDate,
            'paymentSince' => $paymentSince,
        ];
    }
}
