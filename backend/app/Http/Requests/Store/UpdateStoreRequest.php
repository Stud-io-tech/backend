<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',

            'image' => 'nullable|image',

            'pix_key' => 'nullable|string|max:255',

            'schedules' => 'sometimes|required|string',

            'is_open' => 'sometimes|boolean',
            'is_delivered' => 'sometimes|boolean',

            'delivery_time_km' => 'nullable|integer|min:1',
            'dynamic_freight_km' => 'nullable|numeric|min:0',
        ];
    }
}
