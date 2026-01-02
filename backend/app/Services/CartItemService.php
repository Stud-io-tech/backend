<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use League\Config\Exception\ValidationException;

class CartItemService
{
    public function store(array $data)
    {

        return DB::transaction(function () use ($data) {

            if ($data['amount'] < 1) {
                throw new Exception('Quantidade inválida.');
            }

            $product = Product::find($data['product_id']);

            if (!$product) {
                throw new Exception('Produto não encontrado.');
            }

            if ($data['amount'] > $product->amount) {
                throw new Exception('Quantidade solicitada maior que o estoque disponível.');
            }

            $currentCartItem = CartItem::where('user_id', $data['user_id'])
                ->where('product_id', $data['product_id'])
                ->where('active', true)
                ->first();

            $currentAmount = $currentCartItem?->amount ?? 0;
            $newAmount = $currentAmount + $data['amount'];

            if ($currentCartItem) {
                if ($newAmount > $product->amount) {
                    throw new Exception('Quantidade solicitada maior que o estoque disponível.');
                }

                $currentCartItem->increment('amount', $data['amount']);
                return $currentCartItem->refresh();
            }

            return CartItem::create($data);
        });
    }
}