<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInstitutionRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'min:3',
                'max:190',
                //'unique:category,name,NULL,id,deleted_at,NULL',
                Rule::unique('institution')->where(function ($query) {
                    return $query->where('city_id', $this->city_id);
                })->ignore($institutionId),
            ],
            'mail' => ['required', 'email', 'max:190'],
            'city_id' => 'required|exists:city,id',
        ];
    }
    
    public function messages()
    {
        return [
            'name.unique' => 'El nombre de esa institucion ya existe en esa ciudad',
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser un texto',
            'name.max' => 'El nombre no debe exceder los 190 caracteres',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email debe ser un email valido',
            'email.max' => 'El email no debe exceder los 190 caracteres',
            'city_id.required' => 'La ciudad es requerida',
            'city_id.integer' => 'La ciudad debe ser un numero',
        ];
    }
}
