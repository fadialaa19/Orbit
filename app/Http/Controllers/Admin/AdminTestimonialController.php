<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::with('user')->latest()->get();
        $students = User::students()->select('id', 'name')->get();
        return view('admin.testimonials', compact('testimonials', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'university' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'avatar' => 'nullable|url',
            'user_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        Testimonial::create($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'تم إضافة التجربة بنجاح');
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'university' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'avatar' => 'nullable|url',
            'user_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $testimonial->update($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'تم تحديث التجربة بنجاح');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return redirect()->route('admin.testimonials.index')
            ->with('success', 'تم حذف التجربة بنجاح');
    }

    public function toggleStatus(Testimonial $testimonial)
    {
        $testimonial->update(['is_active' => !$testimonial->is_active]);
        return redirect()->route('admin.testimonials.index')
            ->with('success', 'تم تغيير الحالة بنجاح');
    }
}

