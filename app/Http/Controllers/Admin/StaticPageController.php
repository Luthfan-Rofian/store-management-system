<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use App\Models\Faq;
use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    public function edit()
    {
        $page = StaticPage::first();
        if (!$page) {
            $page = StaticPage::create([
                'about_title' => 'Tentang Kami',
                'about_content' => '',
                'terms_content' => '',
                'privacy_content' => '',
            ]);
        }

        $faqs = Faq::latest()->get();

        return view('admin.static-pages.edit', compact('page', 'faqs'));
    }

    public function update(Request $request)
    {
        $page = StaticPage::first();

        $validated = $request->validate([
            'about_title'     => 'nullable|string|max:255',
            'about_content'   => 'nullable|string',
            'terms_content'   => 'nullable|string',
            'privacy_content' => 'nullable|string',
        ]);

        $page->update($validated);

        return redirect()->route('admin.static-pages.edit')->with('success', 'Halaman statis berhasil diperbarui!');
    }
}
