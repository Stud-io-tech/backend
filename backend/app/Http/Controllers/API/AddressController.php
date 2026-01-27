<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\AddressCreateRequest;
use App\Http\Requests\Address\AddressUpdateRequest;
use App\Models\Address;
use App\Models\Store;
use App\Models\User;
use App\Services\AddressService;

class AddressController extends Controller
{
    public function __construct(
        private AddressService $addressService
    )
    {}

    public function store(AddressCreateRequest $request)
    {
        $address = $this->addressService->store($request->validated());

        return response(['address' => $address], 201);
    }

    public function update(AddressUpdateRequest $request, Address $address)
    {
        $address = $this->addressService->update($request->validated(), address: $address);

        return response(['address' => $address], 200);
    }

    public function userAddress(User $user)
    {
        return response(['address' => $user->address()->get()], 200);
    }

    public function storeAddress(Store $store)
    {
        return response(['address' => $store->address()->get()], 200);
    }
}
