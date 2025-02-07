<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuthorRequest extends FormRequest
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
            //
            'name' => 'required|string|max:255|unique:authors,name,' . $this->route('author')->id,
            'position' => 'required|string|max:255',
            'detail' => 'nullable|string|max:255|unique:authors,detail,' . $this->route('author')->id,
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'author_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ];
    }
}
