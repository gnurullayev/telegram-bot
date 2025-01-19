<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CategoryUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['numeric', 'required'],
            'name' => ['string', 'max:255', 'required'],
            'description' => ['string', 'nullable'],
            'short_content' => ['string', 'nullable'],
            'poster_url' => ['file', 'nullable', 'mimes:jpeg,png,jpg', 'max:5000'],
            'is_active' => ['integer', 'required'],
        ];
    }
}
