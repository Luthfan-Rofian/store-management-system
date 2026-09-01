<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderTemplate;
use Illuminate\Http\Request;

class OrderTemplateController extends Controller
{
    public function index()
    {
        $templates = OrderTemplate::latest()->paginate(10);
        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        OrderTemplate::create($request->all());

        return redirect()->route('admin.templates.index')->with('success', 'Template catatan berhasil ditambahkan.');
    }

    public function edit(OrderTemplate $template)
    {
        return view('admin.templates.edit', compact('template'));
    }

    public function update(Request $request, OrderTemplate $template)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $template->update($request->all());

        return redirect()->route('admin.templates.index')->with('success', 'Template catatan berhasil diperbarui.');
    }

    public function destroy(OrderTemplate $template)
    {
        $template->delete();
        return redirect()->route('admin.templates.index')->with('success', 'Template catatan berhasil dihapus.');
    }
}
