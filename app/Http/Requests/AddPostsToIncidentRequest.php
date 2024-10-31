<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddPostsToIncidentRequest extends FormRequest
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
            'posts' => ['required', 'array', 'min:1'],
            'posts.*' => ['required', 'integer', 'exists:post,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'posts.required' => 'Debe seleccionar al menos un post',
            'posts.*.required' => 'Debe seleccionar al menos un post',
            'posts.*.integer' => 'El post seleccionado no es válido',
            'posts.*.exists' => 'El post seleccionado no es válido',
        ];
    }
}
