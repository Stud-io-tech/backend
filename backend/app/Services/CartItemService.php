<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Store;
use Exception;
use Illuminate\Support\Facades\DB;

class CartItemService
{

    public function show(string $id)
    {
        return CartItem::findOrFail($id);
    }
    public function getGroupByStoreByUser(string $user_id)
    {
        $listItems = CartItem::with(['product.store.address'])
            ->where('user_id', $user_id)
            ->where('active', true)
            ->get()
            ->filter(fn($item) => $item->product?->active === true)
            ->map(function ($item) {
                if ($item->amount > $item->product->amount) {
                    $item->amount = $item->product->amount;
                }
                return $item;
            })
            ->filter(fn($item) => $item->amount > 0);

        return $listItems
            ->groupBy(fn($item) => $item->product->store_id)
            ->map(function ($items, $storeId) {

                $store = $items->first()->product->store;
                $address = $store->address;

                return [
                    'store_id' => $storeId,
                    'store_name' => $store->name,
                    'store_is_open' => $store->is_open,
                    'store_latitude' => $address?->latitude,
                    'store_longitude' => $address?->longitude,
                    'store_freight' => $store->dynamic_freight_km,
                    'store_is_delivered' => $store->is_delivered,
                    'store_whatsapp' => $store->address?->whatsapp,
                    'store_owner_name' => $store->user?->name,
                    'store_pix' => $store->pix_key,
                    'store_city' => $store->address?->city,
                    'total' => number_format(
                        $items->sum(fn($item) => $item->product->price * $item->amount),
                        2,
                        '.',
                        ''
                    ),
                    'min_preparation_time' => $items->sum(
                        fn($item) =>
                        $item->product->preparation_time ?? 0
                    ),
                    'max_preparation_time' => $items->sum(
                        fn($item) =>
                        ($item->product->preparation_time ?? 0) * $item->amount
                    ),
                    'store_delivery_time_km' => $store->delivery_time_km,
                    'cart_items' => $items->map(fn($item) => [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'amount' => $item->amount,
                        'price' => $item->product->price,
                        'image' => $item->product->image,
                        'name' => $item->product->name,
                    ])->values(),
                ];
            })
            ->values();
    }

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

    public function updateAmount(array $data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            if ($data['amount'] < 1) {
                throw new Exception('Quantidade inválida.');
            }

            $cartItem = CartItem::findOrFail($id);

            $product = Product::find($cartItem->product_id);

            if (!$product) {
                throw new Exception('Produto não encontrado.');
            }

            if ($data['amount'] > $product->amount) {
                throw new Exception('Quantidade solicitada maior que o estoque disponível.');
            }

            $cartItem->update([
                'amount' => $data['amount'],
            ]);

            return $cartItem->refresh();
        });
    }


    public function destroy(string $id)
    {

        $deleted = CartItem::where('id', $id)->delete();

        if ($deleted === 0) {
            throw new Exception('Item do carrinho não encontrado.');
        }
    }


    public function approveOrderByStore(string $userId, string $storeId): void
    {
        DB::transaction(function () use ($userId, $storeId) {

            $cartItems = CartItem::with('product')
                ->where('user_id', $userId)
                ->where('active', true)
                ->whereHas('product', function ($query) use ($storeId) {
                    $query->where('store_id', $storeId);
                })
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {
                throw new Exception(
                    'Não há itens deste estabelecimento no carrinho.'
                );
            }

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;

                if (!$product || !$product->active) {
                    throw new Exception(
                        'Produto indisponível no momento.'
                    );
                }

                if ($product->amount < 1) {
                    throw new Exception(
                        "Produto {$product->name} sem estoque."
                    );
                }

                $finalAmount = min(
                    $cartItem->amount,
                    $product->amount
                );

                $product->decrement('amount', $finalAmount);

                $cartItem->update([
                    'amount' => $finalAmount,
                    'active' => false,
                ]);
            }
        });
    }

}





