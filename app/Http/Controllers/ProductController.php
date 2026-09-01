<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk (halaman katalog utama).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category'); // Parameter ID kategori bawaan
        $kategoriSlug = $request->input('kategori'); // Parameter slug kategori dari menu popup

        $query = Product::query();

        // Filter berdasarkan pencarian nama atau deskripsi
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter berdasarkan category_id (jika dikirim angka ID)
        if ($category) {
            $query->where('category_id', $category);
        }

        // Filter berdasarkan slug kategori (jika dikirim dari menu pop-up teks seperti 'sewa-vps')
        if ($kategoriSlug) {
            $query->whereHas('category', function($q) use ($kategoriSlug) {
                $q->where('slug', $kategoriSlug)
                  ->orWhere('name', 'LIKE', '%' . str_replace('-', ' ', $kategoriSlug) . '%');
            });
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        // Ambil daftar kategori dari tabel categories untuk sidebar
        $categoriesList = Category::all();

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        $storeSetting = StoreSetting::first();
        // Konversi ke objek agar bisa konsisten digunakan dengan format -> atau []
        $settings = $storeSetting;

        return view('catalog', compact('products', 'categoriesList', 'paymentMethods', 'storeSetting', 'settings'));
    }

    /**
     * Menampilkan halaman detail produk.
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        if (Schema::hasColumn('products', 'views')) {
            $product->increment('views');
        }

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        $storeSetting = StoreSetting::first();
        $settings = $storeSetting;

        return view('product-detail', compact('product', 'paymentMethods', 'storeSetting', 'settings'));
    }
}
