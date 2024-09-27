<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreRegionRequest extends FormRequest
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
        $institutionId = $this->route('institution') ? $this->route('institution')->id : null;
        return [
            'puntos' => 'required|array',
            'name' => [
                'required',
                'string',
                'min:3',
                'max:190',
                Rule::unique('region')->where(function ($query) {
                    return $query->where('institution_id', $this->institution_id);
                })->ignore($institutionId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'puntos.required' => 'El polígono es requerido',
            'puntos.array' => 'Los puntos deben ser un conjunto de coordenadas',
        ];
    }
}
