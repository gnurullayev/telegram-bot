<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MovieUpdateRequest extends FormRequest
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
            'id' => ['string', 'required'],
            'title' => ['string', 'max:255', 'required'],
            'release_date' => ['string', 'required'],
            'region_id' => ['string', 'required'],
            'category_id' => ['string', 'nullable'],
            'duration' => ['integer', 'required'],
            'is_active' => ['integer', 'required'],
            // 'genre' => ['string', 'required'],
            'description' => ['string', 'nullable'],
            'short_content' => ['string', 'nullable'],
            'poster_url' => ['file', 'nullable', 'mimes:jpeg,png,jpg', 'max:5000'],
            'video_url' => ['string', 'max:255', 'required'],
            'genres' => ['array', 'nullable'],
            'tags' => ['array', 'nullable'],
        ];
    }
}
