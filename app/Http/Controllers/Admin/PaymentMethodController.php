<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('sort_order', 'asc')->paginate(15);
        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('admin.payment-methods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'account_name'   => 'nullable|string|max:150',
            'qris_string'    => 'nullable|string',
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:1024',
            'qris_image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'sort_order'     => 'integer',
            'is_active'      => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Handle upload Logo
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('payment-logos', 'public');
        }

        // Handle upload QRIS Image
        if ($request->hasFile('qris_image')) {
            $validated['qris_image'] = $request->file('qris_image')->store('qris-images', 'public');
        }

        PaymentMethod::create($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'account_name'   => 'nullable|string|max:150',
            'qris_string'    => 'nullable|string',
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:1024',
            'qris_image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'sort_order'     => 'integer',
            'is_active'      => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Handle update Logo
        if ($request->hasFile('logo')) {
            if ($paymentMethod->logo) {
                Storage::disk('public')->delete($paymentMethod->logo);
            }
            $validated['logo'] = $request->file('logo')->store('payment-logos', 'public');
        }

        // Handle update QRIS Image
        if ($request->hasFile('qris_image')) {
            if ($paymentMethod->qris_image) {
                Storage::disk('public')->delete($paymentMethod->qris_image);
            }
            $validated['qris_image'] = $request->file('qris_image')->store('qris-images', 'public');
        }

        $paymentMethod->update($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->logo) {
            Storage::disk('public')->delete($paymentMethod->logo);
        }

        if ($paymentMethod->qris_image) {
            Storage::disk('public')->delete($paymentMethod->qris_image);
        }

        $paymentMethod->delete();

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil dihapus.');
    }
}
