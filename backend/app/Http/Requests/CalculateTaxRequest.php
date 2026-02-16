<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CalculateTaxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transactions' => ['required', 'array'],

            'transactions.*.coin'   => ['required', 'string'],
            'transactions.*.type'   => ['required', Rule::in(['buy', 'sell', 'trade', 'transfer'])],
            'transactions.*.amount' => ['required', 'numeric', 'min:0'],
            'transactions.*.price'  => ['required', 'numeric', 'min:0'],
            'transactions.*.date'   => ['required', 'date'],
            'transactions.*.wallet' => ['required', 'string'],

            // Trade-specific
            'transactions.*.from_coin' => [
                'string',
                Rule::requiredIf(fn () => request()->input('transactions.*.type') === 'trade'),
            ],
            'transactions.*.to_coin' => [
                'string',
                Rule::requiredIf(fn () => request()->input('transactions.*.type') === 'trade'),
            ],

            // Transfer-specific
            'transactions.*.from_wallet' => [
                'string',
                Rule::requiredIf(fn () => request()->input('transactions.*.type') === 'transfer'),
            ],
            'transactions.*.to_wallet' => [
                'string',
                Rule::requiredIf(fn () => request()->input('transactions.*.type') === 'transfer'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'transactions.required' => 'Transactions data is required.',
            'transactions.array' => 'Transactions must be an array.',

            'transactions.*.coin.required' => 'Coin is required for each transaction.',
            'transactions.*.type.required' => 'Transaction type is required.',
            'transactions.*.type.in' => 'Transaction type must be buy, sell, trade, or transfer.',

            'transactions.*.amount.required' => 'Amount is required.',
            'transactions.*.amount.numeric' => 'Amount must be a number.',

            'transactions.*.price.required' => 'Price is required.',
            'transactions.*.price.numeric' => 'Price must be a number.',

            'transactions.*.date.required' => 'Date is required.',
            'transactions.*.date.date' => 'Date must be a valid date.',

            'transactions.*.wallet.required' => 'Wallet is required.',
        ];
    }
}
