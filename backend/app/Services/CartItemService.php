<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Store;
use Exception;
use Illuminate\Support\Facades\DB;

class CartItemService
{

    private CartItem $cartItem;

    public static function getGroupByStoreByUser(string $user_id)
    {
        $listItems = CartItem::where('user_id', $user_id)->where('active', true)
            ->orderBy('created_at', 'desc')->get();

        return $listItems
            ->groupBy(fn($item) => $item->product->store_id)
            ->map(function ($items, $storeId) {

                $store = $items->first()->product->store;
                return [

                    'store_id' => $storeId,
                    'store_name' => $store->name,
                    'total' => $items->sum(
                        fn($item) =>
                        $item->product->price * $item->amount
                    ),
                    'cart_items' => $items->map(fn($item) => [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'amount' => $item->amount,
                        'active' => $item->active,
                        'price' => $item->product->price,
                        'image' => $item->product->image,
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


    public function destroy(string $id)
    {

        $deleted = CartItem::where('id', $id)->delete();

        if ($deleted === 0) {
            throw new Exception('Item do carrinho não encontrado.');
        }
    }
}