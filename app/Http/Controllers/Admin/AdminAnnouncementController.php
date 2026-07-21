<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AdminAnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::ordered()->get();
        return view('admin.announcements', compact('announcements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'icon' => 'nullable|string|max:10',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'type' => 'required|in:info,warning,urgent',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['icon'] = $validated['icon'] ?: '📢';
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Announcement::create($validated);

        return redirect()->route('admin.announcements.index')->with('success', 'تم إضافة الإعلان بنجاح');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'icon' => 'nullable|string|max:10',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'type' => 'required|in:info,warning,urgent',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['icon'] = $validated['icon'] ?: '📢';
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')->with('success', 'تم تحديث الإعلان بنجاح');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'تم حذف الإعلان بنجاح');
    }

    public function toggleStatus(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);

        return redirect()->route('admin.announcements.index')->with('success', 'تم تغيير حالة الإعلان بنجاح');
    }
}
