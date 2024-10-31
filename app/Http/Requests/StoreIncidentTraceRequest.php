<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentTraceRequest extends FormRequest
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
            'status_id' => 'required|integer|exists:incident_status,id',
            'description' => 'present|string|nullable|max:190',
        ];
    }

    public function messages(){
        return [
            'status_id.required' => 'El estado es requerido',
            'status_id.integer' => 'El estado debe ser un número',
            'status_id.exists' => 'El estado no existe',
            'descripction.present' => 'La descripción tiene que estar presente en el formulario',
            'description.string' => 'La descripción tiene que ser un texto',
            'description.max' => 'La descripción no puede tener más de 190 caracteres',
        ];
    }
}
