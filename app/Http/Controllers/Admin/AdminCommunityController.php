<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCommunityController extends Controller
{
    public function index()
    {
        $communities = Community::withCount('messages')->latest()->get();
        return view('admin.communities', compact('communities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:announcement,discussion',
            'icon' => 'nullable|string|max:10',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = true;

        Community::create($validated);

        return redirect()->route('admin.communities.index')->with('success', 'تم إنشاء المجتمع بنجاح');
    }

    public function update(Request $request, Community $community)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:announcement,discussion',
            'icon' => 'nullable|string|max:10',
        ]);

        $community->update($validated);

        return redirect()->route('admin.communities.index')->with('success', 'تم تحديث المجتمع بنجاح');
    }

    public function destroy(Community $community)
    {
        $community->messages()->delete();
        $community->delete();

        return redirect()->route('admin.communities.index')->with('success', 'تم حذف المجتمع بنجاح');
    }

    public function toggleStatus(Community $community)
    {
        $community->update(['is_active' => !$community->is_active]);
        return redirect()->route('admin.communities.index')->with('success', 'تم تغيير حالة المجتمع بنجاح');
    }
}
