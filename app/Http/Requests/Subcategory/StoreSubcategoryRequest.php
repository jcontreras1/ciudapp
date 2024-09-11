<?php

namespace App\Http\Requests\Subcategory;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreSubcategoryRequest extends FormRequest
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
        $subcategoria = $this->route('subcategory') ? $this->route('subcategory')->id : null; //Para el update
        $categoria = $this->route('category')->id; //Para la categoria
        
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:190',
                Rule::unique('subcategory', 'name')
                ->where(fn(Builder $query) => $query->where('category_id', $categoria))
                ->whereNull('deleted_at')
                ->ignore($subcategoria),
            ],
            'icon' => 'required|string|min:3|max:190',
        ];
    }
}
