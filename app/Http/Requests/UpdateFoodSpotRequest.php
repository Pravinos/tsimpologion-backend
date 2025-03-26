<?php
// filepath: /C:/tsimpologion-app/tsimpologion-backend/app/Http/Requests/UpdateFoodSpotRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFoodSpotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category' => 'sometimes|string|max:255',
            'info_link' => 'sometimes|string|max:500',
            'rating' => 'nullable|numeric|min:0|max:5',
        ];
    }
}