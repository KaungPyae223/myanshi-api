<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:authors,name',
            'position' => 'required|string|max:255',
            'detail' => 'required|string|max:255|unique:authors,detail',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'author_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
