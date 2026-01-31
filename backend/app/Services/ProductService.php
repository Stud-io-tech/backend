<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{

    private Product $product;

    public static function index()
    {
        $products = Product::where('active', true)
            ->whereHas('store', function ($query) {
                $query->where('active', true);
            })
            ->orderBy('created_at', 'desc')->get();

        return $products;
    }

    public static function getByStore(string $store_id)
    {
        $products = Product::where('store_id', $store_id)->where('active', true)
            ->orderBy('created_at', 'desc')->get();

        return $products;
    }

    public function store(array $data)
    {
        $this->product = Product::create($data);

        return $this->product;
    }

    public function update(array $data, Product $product)
    {
        $this->product = $product;

        $this->product->update($data);

        return $this->product;
    }


    public function destroy(Product $product)
    {
        $this->product = $product;

        $this->product->delete();

        return $this->product;
    }

    public function changeActive(Product $product)
    {

        $this->product = $product;

        $this->product->update([
            'active' => !$product->active,
        ]);

        return $this->product;
    }

    public static function getDisabled(string $storeId)
    {
        $products = Product::where('store_id', $storeId)->where('active', false)
            ->orderBy('created_at', 'desc')->get();

        return $products;
    }
}
