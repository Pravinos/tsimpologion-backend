<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFoodSpotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'string',
            'info_link' => 'string|max:500',
            'rating' => 'nullable|numeric|min:0|max:5',
            'owner_id' => 'nullable|numeric',
        ];
    }
}