<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexPetBreedRequest extends FormRequest
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
            'pet_type_id' => ['required', 'exists:pet_types,id'], // Link to pet type
            'sort' => 'in:nickname_uk,nickname_en,nickname_ru,created_at',  // Only allowed fields
            'order' => 'in:asc,desc',        // ASC or DESC
            'per_page' => 'integer|min:1|max:100', // Pagination limit
            'page' => 'integer|min:1', // Page number
        ];
    }
}
