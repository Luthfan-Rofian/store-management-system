<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalOrders   = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();

        $latestOrders = Order::with('product')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'pendingOrders',
            'latestOrders'
        ));
    }
}
