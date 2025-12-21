<?php

namespace App\Http\Requests\Address;

use App\Models\Address;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        $address = $this->route('address'); // JÁ É O MODEL


        if (! $address instanceof Address) {
            return false;
        }

        if ($this->filled('user_id')) {
            return $address->user_id === $this->input('user_id');
        }

        if ($this->filled('store_id')) {
            return $address->store_id === $this->input('store_id');
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'nullable',
                'exists:users,id',
                'required_without:store_id',
                // 'prohibited_with:store_id',
            ],

            'store_id' => [
                'nullable',
                'exists:stores,id',
                'required_without:user_id',
                // 'prohibited_with:user_id',
            ],
            'cep' => ['required', 'string', 'max:8'],
            'state' => ['required', 'string', 'max:2'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'street' => ['required', 'string', 'max:255'],
            'complement' => ['nullable', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:10'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'latitude' => ['nullable', 'string'],
            'longitude' => ['nullable', 'string'],
        ];
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'The address is not authorized for this action.'
            ], 401)
        );
    }
}
