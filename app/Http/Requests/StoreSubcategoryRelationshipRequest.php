<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubcategoryRelationshipRequest extends FormRequest
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
            'origin_id' => ['required', 'integer', 'exists:subcategory,id'],
            'destiny_id' => ['required', 'integer', 'exists:subcategory,id'],
            'percentage' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array{
        return [
            'origin_id.required' => 'El campo origen es requerido',
            'origin_id.integer' => 'El campo origen debe ser un número entero',
            'origin_id.exists' => 'El campo origen no existe en la base de datos',
            'destiny_id.required' => 'El campo destino es requerido',
            'destiny_id.integer' => 'El campo destino debe ser un número entero',
            'destiny_id.exists' => 'El campo destino no existe en la base de datos',
            'percentage.required' => 'El campo porcentaje es requerido',
            'percentage.integer' => 'El campo porcentaje debe ser un número entero',
            'percentage.min' => 'El campo porcentaje debe ser mayor o igual a 0',
            'percentage.max' => 'El campo porcentaje debe ser menor o igual a 100',
        ];
    }
}
