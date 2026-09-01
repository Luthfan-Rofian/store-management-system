<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTemplate; // <-- 1. Tambahkan use Model OrderTemplate di sini
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['product', 'paymentMethod'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone_number', 'like', '%' . $request->search . '%');
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['product', 'paymentMethod']);
        $templates = OrderTemplate::all(); // <-- 2. Ambil semua data template
        return view('admin.orders.show', compact('order', 'templates')); // <-- 3. Kirim $templates ke view
    }

    public function print(Order $order)
    {
        $order->load(['product', 'paymentMethod']);
        return view('admin.orders.print', compact('order'));
    }

    /**
     * Memproses penyimpanan perubahan status dan catatan/paket admin secara permanen.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
            'admin_notes' => 'nullable|string',
        ]);

        $order->update([
            'status' => $request->status,
            'notes'  => $request->admin_notes,
        ]);

        return back()->with('success', 'Status dan paket item berhasil diperbarui!');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Data pesanan berhasil dihapus.');
    }
}
