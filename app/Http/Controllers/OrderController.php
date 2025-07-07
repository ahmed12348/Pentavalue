<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\OrderPlaced;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'quantity'   => 'required|integer|min:1',
            'price'      => 'required|numeric|min:0',
            'date'       => 'required|date',
        ]);

        DB::statement(
            "INSERT INTO orders (product_id, quantity, price, date) VALUES (?, ?, ?, ?)",
            [
                $validated['product_id'],
                $validated['quantity'],
                $validated['price'],
                $validated['date'],
            ]
        );

        $order = [
            'product_id' => $validated['product_id'],
            'quantity'   => $validated['quantity'],
            'price'      => $validated['price'],
            'date'       => $validated['date'],
        ];

        event(new OrderPlaced($order));

        // (Later: Broadcast event here)

        return response()->json(['message' => 'Order stored successfully'], 201);
    }
}
