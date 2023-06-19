<?php

namespace App\Filters;

class InvoiceFilter extends Filter
{
    protected array $allowedOperatorsFields = [
        'value' => ['gt', 'gte', 'lt', 'lte', 'eq', 'ne', 'in'],
        'type' => ['eq', 'ne', 'in'],
        'paid' => ['eq', 'ne'],
        'payment_date' => ['gt', 'gte', 'lt', 'lte', 'eq', 'ne', 'in'],
    ];
}
