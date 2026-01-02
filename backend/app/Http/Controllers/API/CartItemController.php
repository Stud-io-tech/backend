<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartItem\CartItemRequest;
use App\Services\CartItemService;
use Exception;
use Illuminate\Http\Request;

class CartItemController extends Controller
{

    public function __construct(protected CartItemService $cartItemService)
    {

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CartItemRequest $request)
    {
        try {
            $cartItem = $this->cartItemService->store([

                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'amount' => $request->amount,

            ], );

            return response(['cartItems' => $cartItem], 201);
        } catch (Exception $e) {
            return response(['message' => $e], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
