<?php
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
            'category' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'info_link' => 'sometimes|string|max:500',
            'rating' => 'nullable|numeric|min:0|max:5',
            'owner_id' => 'nullable|numeric',
            'phone' => 'sometimes|nullable|string|max:30',
            'business_hours' => 'sometimes|nullable|array',
            'social_links' => 'sometimes|nullable|array',
            'price_range' => 'sometimes|nullable|string|in:$,$$,$$$,$$$$',
        ];
    }
}
