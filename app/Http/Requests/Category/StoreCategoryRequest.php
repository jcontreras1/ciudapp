<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
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
        $id = $this->route('category') ? $this->route('category')->id : null;
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:190',
                'unique:category,name,NULL,id,deleted_at,NULL',
                //Rule::unique('category', 'name')->ignore($id),
            ],
            'icon' => 'required|string|min:3|max:190',
        ];
    }
}
