<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class IndexPetOwnerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(Request $request): bool
    {
        $token = $request->bearerToken();
        if (!$token) {
            return false;
        }

        // Get the user by token
        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken) {
            return false;
        }

        $user = $accessToken->tokenable; // Get the user
        return !empty($user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|exists:users,id',  // Link to user - owner
            'sort' => 'in:nickname_uk,nickname_en,nickname_ru,created_at',  // Only allowed fields
            'order' => 'in:asc,desc',        // ASC or DESC
            'per_page' => 'integer|min:1|max:100', // Pagination limit
            'page' => 'integer|min:1', // Page number
        ];
    }
}
