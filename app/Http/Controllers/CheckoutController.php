<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Helpers\PhoneHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Proses order dari Offcanvas Checkout (Satuan), simpan ke DB, lalu balikan link WhatsApp
     */
    public function store(Request $request)
    {
        // 1. Validasi input (disesuaikan dengan nama field dari JS di product-detail.blade.php)
        $validated = $request->validate([
            'product_id'          => 'required|exists:products,id',
            'quantity'            => 'required|integer|min:1',
            'customer_name'       => 'required|string|max:255',
            'phone_number'        => 'required|string|max:20',
            'email'               => 'nullable|email|max:255',
            'address'             => 'required|string',
            'notes'               => 'nullable|string',
            'payment_method_id'   => 'nullable|exists:payment_methods,id',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Cek stok masih tersedia
        if ($product->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Stok produk tidak mencukupi.'
            ], 422);
        }

        $paymentMethod = null;
        if (!empty($validated['payment_method_id'])) {
            $paymentMethod = PaymentMethod::find($validated['payment_method_id']);
        }

        // Normalisasi nomor HP pemesan (08xxx -> 628xxx)
        $normalizedCustomerPhone = PhoneHelper::normalize($validated['phone_number']);

        // Harga dihitung ulang di server (jangan percaya harga dari client)
        $productPrice = $product->price;
        $totalPrice = $productPrice * $validated['quantity'];

        // 2. Simpan data pesanan ke database
        $order = DB::transaction(function () use ($validated, $product, $paymentMethod, $normalizedCustomerPhone, $productPrice, $totalPrice) {
            $order = Order::create([
                'order_code'          => Order::generateOrderCode(),
                'product_id'          => $product->id,
                'product_name'        => $product->name,
                'product_price'       => $productPrice,
                'qty'                 => $validated['quantity'],
                'total_price'         => $totalPrice,
                'customer_name'       => $validated['customer_name'],
                'customer_phone'      => $normalizedCustomerPhone,
                'customer_email'      => $validated['email'] ?? null,
                'customer_address'    => $validated['address'],
                'order_note'          => $validated['notes'] ?? null,
                'payment_method_id'   => $paymentMethod?->id,
                'payment_method_name' => $paymentMethod?->name,
                'status'              => 'pending',
            ]);

            // Kurangi stok produk
            $product->decrement('stock', $validated['quantity']);

            return $order;
        });

        // 3. Susun format teks pesan WhatsApp
        $waMessage = $this->buildWhatsAppMessage($order, $paymentMethod);

        // 4. Ambil nomor WA Admin Toko dari settingan
        $storeSetting = StoreSetting::first();
        $rawStoreWa = $storeSetting->whatsapp_number ?? '6281234567890';
        $storeWhatsapp = PhoneHelper::normalize($rawStoreWa);

        $waUrl = "https://wa.me/{$storeWhatsapp}?text=" . rawurlencode($waMessage);

        return response()->json([
            'success'      => true,
            'message'      => 'Pesanan Anda sedang diteruskan ke WhatsApp...',
            'redirect_url' => $waUrl,
        ]);
    }

    /**
     * Proses order dari Keranjang Belanja, simpan ke DB, lalu balikan link WhatsApp
     */
    public function processCartCheckout(Request $request)
    {
        // 1. Validasi input form keranjang
        $validated = $request->validate([
            'customer_name'       => 'required|string|max:255',
            'customer_phone'      => 'required|string|max:20',
            'customer_email'      => 'nullable|email|max:255',
            'customer_address'    => 'required|string',
            'notes'               => 'nullable|string',
            'payment_method_id'   => 'nullable|exists:payment_methods,id',
            'cart_data'           => 'required|string', // Data JSON keranjang dari client
        ]);

        $cartItems = json_decode($validated['cart_data'], true);
        if (empty($cartItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja Anda kosong.'
            ], 422);
        }

        $paymentMethod = null;
        if (!empty($validated['payment_method_id'])) {
            $paymentMethod = PaymentMethod::find($validated['payment_method_id']);
        }

        // Normalisasi nomor HP pemesan
        $normalizedCustomerPhone = PhoneHelper::normalize($validated['customer_phone']);

        // Hitung total keseluruhan dan susun teks daftar produk
        $grandTotal = 0;
        $itemsText = "";

        foreach ($cartItems as $i => $item) {
            $subtotal = floatval($item['price']);
            $grandTotal += $subtotal;
            $num = $i + 1;
            $itemsText .= "{$num}. *{$item['name']}* - Rp " . number_format($subtotal, 0, ',', '.') . "\n";
        }

        // Ambil ID produk pertama dari keranjang (jika ada) untuk mengisi kolom product_id
        $firstProductId = !empty($cartItems[0]['id']) ? $cartItems[0]['id'] : Product::first()?->id;

        // 2. Simpan data pesanan keranjang ke database
        $order = DB::transaction(function () use ($validated, $paymentMethod, $normalizedCustomerPhone, $grandTotal, $cartItems, $firstProductId) {
            $order = Order::create([
                'order_code'          => Order::generateOrderCode(),
                'product_id'          => $firstProductId,
                'product_name'        => 'Pesanan Keranjang (' . count($cartItems) . ' Produk)',
                'product_price'       => $grandTotal,
                'qty'                 => count($cartItems),
                'total_price'         => $grandTotal,
                'customer_name'       => $validated['customer_name'],
                'customer_phone'      => $normalizedCustomerPhone,
                'customer_email'      => $validated['customer_email'] ?? null,
                'customer_address'    => $validated['customer_address'],
                'order_note'          => $validated['notes'] ?? null,
                'payment_method_id'   => $paymentMethod?->id,
                'payment_method_name' => $paymentMethod?->name,
                'status'              => 'pending',
            ]);

            return $order;
        });

        // 3. Susun format teks pesan WhatsApp untuk keranjang
        $waMessage = $this->buildCartWhatsAppMessage($order, $itemsText, $grandTotal, $paymentMethod);

        // 4. Ambil nomor WA Admin Toko dari settingan
        $storeSetting = StoreSetting::first();
        $rawStoreWa = $storeSetting->whatsapp_number ?? '6281234567890';
        $storeWhatsapp = PhoneHelper::normalize($rawStoreWa);

        $waUrl = "https://wa.me/{$storeWhatsapp}?text=" . rawurlencode($waMessage);

        return response()->json([
            'success'      => true,
            'message'      => 'Pesanan keranjang Anda sedang diteruskan ke WhatsApp...',
            'redirect_url' => $waUrl,
        ]);
    }

    /**
     * Susun format teks pesan WhatsApp untuk satuan
     */
    private function buildWhatsAppMessage(Order $order, ?PaymentMethod $paymentMethod): string
    {
        $lines = [];

        $lines[] = "Halo, saya ingin memesan produk berikut:";
        $lines[] = "";
        $lines[] = "*Kode Pesanan:* {$order->order_code}";
        $lines[] = "*Produk:* {$order->product_name}";
        $lines[] = "*Qty:* {$order->qty}";
        $lines[] = "*Harga Satuan:* Rp" . number_format((float) $order->product_price, 0, ',', '.');
        $lines[] = "*Total Tagihan:* Rp" . number_format((float) $order->total_price, 0, ',', '.');

        $lines[] = "";
        $lines[] = "*Data Pemesan:*";
        $lines[] = "Nama: {$order->customer_name}";
        $lines[] = "No. HP: {$order->customer_phone}";

        if ($order->customer_email) {
            $lines[] = "Email: {$order->customer_email}";
        }

        $lines[] = "Alamat: {$order->customer_address}";

        if ($order->order_note) {
            $lines[] = "Catatan: {$order->order_note}";
        }

        if ($paymentMethod) {
            $lines[] = "";
            $lines[] = "*Metode Pembayaran:*";

            if (!empty($paymentMethod->account_number)) {
                $lines[] = "{$paymentMethod->name} - {$paymentMethod->account_number} a.n {$paymentMethod->account_name}";
            } else {
                $lines[] = "{$paymentMethod->name} (Pembayaran via QRIS)";
            }
        }

        $lines[] = "";
        $lines[] = "Mohon informasi selanjutnya untuk proses pembayaran & pengiriman. Terima kasih.";

        return implode("\n", $lines);
    }

    /**
     * Susun format teks pesan WhatsApp khusus keranjang
     */
    private function buildCartWhatsAppMessage(Order $order, string $itemsText, float $grandTotal, ?PaymentMethod $paymentMethod): string
    {
        $lines = [];

        $lines[] = "Halo, saya ingin memesan produk dari keranjang:";
        $lines[] = "";
        $lines[] = "*Kode Pesanan:* {$order->order_code}";
        $lines[] = "*Daftar Produk:*";
        $lines[] = $itemsText;
        $lines[] = "*Total Tagihan:* Rp" . number_format($grandTotal, 0, ',', '.');

        $lines[] = "";
        $lines[] = "*Data Pemesan:*";
        $lines[] = "Nama: {$order->customer_name}";
        $lines[] = "No. HP: {$order->customer_phone}";

        if ($order->customer_email) {
            $lines[] = "Email: {$order->customer_email}";
        }

        $lines[] = "Alamat: {$order->customer_address}";

        if ($order->order_note) {
            $lines[] = "Catatan: {$order->order_note}";
        }

        if ($paymentMethod) {
            $lines[] = "";
            $lines[] = "*Metode Pembayaran:*";

            if (!empty($paymentMethod->account_number)) {
                $lines[] = "{$paymentMethod->name} - {$paymentMethod->account_number} a.n {$paymentMethod->account_name}";
            } else {
                $lines[] = "{$paymentMethod->name} (Pembayaran via QRIS)";
            }
        }

        $lines[] = "";
        $lines[] = "Mohon informasi selanjutnya untuk proses pembayaran & pengiriman. Terima kasih.";

        return implode("\n", $lines);
    }

    /**
     * Menampilkan halaman form pencarian pesanan untuk pembeli
     */
    public function trackingForm()
    {
        return view('tracking.form');
    }

    /**
     * Memproses pencarian data pesanan berdasarkan kode order atau nomor WhatsApp
     */
    public function trackingResult(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string',
        ]);

        $keyword = $request->keyword;

        $orders = Order::with(['product', 'paymentMethod'])
            ->where('order_code', 'like', "%{$keyword}%")
            ->orWhere('customer_phone', 'like', "%{$keyword}%")
            ->latest()
            ->get();

        return view('tracking.result', compact('orders', 'keyword'));
    }
}
