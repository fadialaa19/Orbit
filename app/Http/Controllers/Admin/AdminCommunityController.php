<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
            'image' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url|max:500',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = true;
        $validated['image'] = $this->resolveImageInput($request);
        unset($validated['image_url']);

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
            'image' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url|max:500',
        ]);

        $newImage = $this->resolveImageInput($request);
        $validated['image'] = $newImage ?? $community->getRawOriginal('image');
        unset($validated['image_url']);

        $community->update($validated);

        return redirect()->route('admin.communities.index')->with('success', 'تم تحديث المجتمع بنجاح');
    }

    /**
     * Communities can be illustrated either by an uploaded file or a pasted
     * external URL. A file upload always wins if both are provided.
     */
    private function resolveImageInput(Request $request): ?string
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
            return $file->storeAs('communities', $filename, 'public');
        }

        return $request->filled('image_url') ? $request->input('image_url') : null;
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
