<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Show the form for editing settings.
     */
    public function edit()
    {
        $settings = StoreSetting::first();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name'            => 'required|string|max:255',
            'whatsapp_number'       => 'required|string|max:20',
            'store_address'         => 'nullable|string|max:1000',
            'description'           => 'nullable|string|max:2000',
            'ketentuan_content'     => 'nullable|string',
            'cara_shopping_content' => 'nullable|string',
            'faq_content'           => 'nullable|string',
            'logo'                  => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        // Ambil data pengaturan yang sudah ada atau buat instance baru
        $storeSetting = StoreSetting::first() ?? new StoreSetting();

        $storeSetting->store_name            = $validated['store_name'];
        $storeSetting->whatsapp_number       = $validated['whatsapp_number'];
        $storeSetting->store_address         = $validated['store_address'] ?? null;
        $storeSetting->description           = $validated['description'] ?? null;
        $storeSetting->ketentuan_content     = $validated['ketentuan_content'] ?? null;
        $storeSetting->cara_shopping_content = $validated['cara_shopping_content'] ?? null;
        $storeSetting->faq_content           = $validated['faq_content'] ?? null;

        // Proses upload logo jika ada file baru yang diunggah
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada untuk menghemat ruang penyimpanan server
            if (!empty($storeSetting->logo) && Storage::disk('public')->exists($storeSetting->logo)) {
                Storage::disk('public')->delete($storeSetting->logo);
            }

            // Simpan logo baru ke folder storage/app/public/logos
            $path = $request->file('logo')->store('logos', 'public');
            $storeSetting->logo = $path;
        }

        $storeSetting->save();

        return redirect()->route('admin.settings.edit')
                         ->with('success', 'Pengaturan toko berhasil diperbarui!');
    }
}
