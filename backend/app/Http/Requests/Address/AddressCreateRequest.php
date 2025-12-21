<?php

namespace App\Http\Requests\Address;

use App\Models\Address;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $userId = $this->input('user_id');
        $storeId = $this->input('store_id');

        if ($userId) {
            return ! Address::where('user_id', $userId)->exists();
        }

        if ($storeId) {
            return ! Address::where('store_id', $storeId)->exists();
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
            'message' => 'Unauthorized. An address for this user or store already exists.'
        ], 401)
    );
    }
}
