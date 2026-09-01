<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category; // <-- 1. WAJIB ADA: Mengimpor model Category
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        // 2. AMBIL DATA KATEGORI
        $categories = Category::all();

        // 3. KIRIM KE VIEW CREATE
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'category_id'    => 'required|exists:categories,id', // <-- Validasi kategori harus dipilih
            'image'          => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'original_price' => 'nullable|numeric|min:0',
            'price'          => 'required|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'description'    => 'nullable|string',
            'specification'  => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($request->name) . '-' . Str::random(5);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        // Ambil juga kategori untuk halaman edit produk
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'category_id'    => 'required|exists:categories,id', // <-- Validasi kategori di edit
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'original_price' => 'nullable|numeric|min:0',
            'price'          => 'required|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'description'    => 'nullable|string',
            'specification'  => 'nullable|string',
        ]);

        // Update slug hanya jika nama berubah
        if ($product->name !== $request->name) {
            $validated['slug'] = Str::slug($request->name) . '-' . Str::random(5);
        }

        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
