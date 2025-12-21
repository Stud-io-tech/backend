<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Store;

class AddressService
{
    private Address $address;

    public function store(array $data)
    {
        $this->address = Address::create($data);

        return $this->address;
    }

    public function update(array $data, Address $address)
    {   
        $this->address = $address;

        $this->address->update($data);
        return $this->address;
    }

    public function destroy(Address $address)
    {   
        $this->address = $address;

        $this->address->delete();
        return $this->address;
    }
}
