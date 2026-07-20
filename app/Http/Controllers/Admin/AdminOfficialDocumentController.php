<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfficialDocument;
use Illuminate\Http\Request;

class AdminOfficialDocumentController extends Controller
{
    public function index()
    {
        $documents = OfficialDocument::ordered()->get();
        return view('admin.official-documents', compact('documents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'icon' => 'nullable|string|max:10',
            'title' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['icon'] = $validated['icon'] ?: '📄';
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        OfficialDocument::create($validated);

        return redirect()->route('admin.official-documents.index')->with('success', 'تم إضافة المستند بنجاح');
    }

    public function update(Request $request, OfficialDocument $officialDocument)
    {
        $validated = $request->validate([
            'icon' => 'nullable|string|max:10',
            'title' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['icon'] = $validated['icon'] ?: '📄';
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $officialDocument->update($validated);

        return redirect()->route('admin.official-documents.index')->with('success', 'تم تحديث المستند بنجاح');
    }

    public function destroy(OfficialDocument $officialDocument)
    {
        $officialDocument->delete();

        return redirect()->route('admin.official-documents.index')->with('success', 'تم حذف المستند بنجاح');
    }

    public function toggleStatus(OfficialDocument $officialDocument)
    {
        $officialDocument->update(['is_active' => !$officialDocument->is_active]);

        return redirect()->route('admin.official-documents.index')->with('success', 'تم تغيير حالة المستند بنجاح');
    }
}
