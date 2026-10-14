<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */

    public function messages(): array
    {
        return[
            'amount.gt' => 'Please enter an amount greater than RM0',
            'amount.max' => 'The amount exceeded the limit',
            'type.required' => 'Please specify transaction type',
            'payment_type.required' => 'Please specify payment type',
            'descriptions.required' => 'Please enter a transaction description.',
        ];
    }

    public function rules(): array
    {
        return [
            
            'descriptions' => 'required|string|max:255',
            'amount' => 'required|numeric|gt:0|max:99999.99|decimal:0,2',
            'input_date' => 'required|date_format:Y-m-d|before_or_equal:today',
            'type' => 'required|in:income,expense',
            'payment_type' => 'required|in:cash,online'
        
        ];
    }
}
