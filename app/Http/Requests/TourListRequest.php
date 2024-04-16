<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TourListRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'priceFrom' => 'nullable|numeric',
            'priceTo' => 'numeric',
            'dateFrom' => 'date',
            'dateTo' => 'date',
            'sortBy' => Rule::in(['price']),
            'sortOrder' => Rule::in(['asc', 'desc']),
        ];
    }

    public function messages(): array
    {
        return [
            //            'priceFrom.numeric' => 'priceFrom должен быть числом',
            //            'priceTo.numeric' => 'priceTo должен быть числом',
            //            'dateFrom.date' => 'dateFrom должен быть датой',
            //            'dateTo.date' => 'dateTo должен быть датой',
            //            'sortBy.in' => 'SortBy параметр должен быть равен "price"',
            //            'sortOrder.in' => 'sortOrder параметр должен быть равен "asc" или "desc"',
            'sortBy' => "The 'sortBy' parameter accepts only 'price' value",
            'sortOrder' => "The 'sortOrder' parameter accepts only 'asc' or 'desc' value",

        ];
    }
}
