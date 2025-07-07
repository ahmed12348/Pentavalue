<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $totalRevenue = DB::selectOne("SELECT COALESCE(SUM(price * quantity), 0) as total FROM orders")->total;

        $topProducts = DB::select("
            SELECT product_id, SUM(quantity) as total_sold
            FROM orders
            GROUP BY product_id
            ORDER BY total_sold DESC
            LIMIT 5
        ");

        $revenueLastMinute = DB::selectOne("
            SELECT COALESCE(SUM(price * quantity), 0) as total
            FROM orders
            WHERE date >= datetime('now', '-1 minute')
        ")->total;

        $ordersLastMinute = DB::selectOne("
            SELECT COUNT(*) as count
            FROM orders
            WHERE date >= datetime('now', '-1 minute')
        ")->count;

        return response()->json([
            'total_revenue' => $totalRevenue,
            'top_products' => $topProducts,
            'revenue_last_minute' => $revenueLastMinute,
            'orders_last_minute' => $ordersLastMinute,
        ]);
    }

    public function recentOrders()
    {
        $recentOrders = DB::select("
            SELECT product_id, quantity, price, date
            FROM orders
            ORDER BY date DESC
            LIMIT 10
        ");

        return response()->json($recentOrders);
    }
}
