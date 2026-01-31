<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateProductRequest extends FormRequest
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
    protected function prepareForValidation(): void
    {
        if ($this->has('is_perishable')) {
            $this->merge([
                'is_perishable' => filter_var($this->is_perishable, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max: 255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'image',
            'is_perishable' => 'sometimes|boolean'
        ];
    }
}
