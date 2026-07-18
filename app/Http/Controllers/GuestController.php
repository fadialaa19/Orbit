<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function scholarships(Request $request)
    {
        $search = $request->get('q');
        $category = $request->get('category');

        $scholarships = Scholarship::active()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title_ar', 'like', "%{$search}%")
                      ->orWhere('title_en', 'like', "%{$search}%")
                      ->orWhere('country', 'like', "%{$search}%")
                      ->orWhere('university', 'like', "%{$search}%");
                });
            })
            ->when($category, function ($query, $category) {
                return $query->whereJsonContains('categories', $category);
            })
            ->paginate(9);

        $scholarships->appends($request->only(['q', 'category']));

        $activeCount = Scholarship::active()->count();

        return view('guest.scholarships', compact('scholarships', 'search', 'category', 'activeCount'));
    }

    public function scholarshipShow(Scholarship $scholarship)
    {
        return view('guest.scholarship-show', compact('scholarship'));
    }

    public function about()
    {
        $studentsCount = User::students()->count();
        $scholarshipsCount = Scholarship::active()->count();
        $universitiesCount = Scholarship::select('university')->distinct()->count();
        $teamMembers = User::admins()->where('status', 'active')->get();

        return view('guest.about', compact('studentsCount', 'scholarshipsCount', 'universitiesCount', 'teamMembers'));
    }

    public function services()
    {
        $studentsCount = User::students()->count();
        $testimonials = \App\Models\Testimonial::active()->get();

        return view('guest.services', compact('studentsCount', 'testimonials'));
    }

    public function contact()
    {
        return view('guest.contact');
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:3000',
        ]);

        $contactMessage = \App\Models\ContactMessage::create($validated);

        try {
            User::admins()->get()->each->notify(new \App\Notifications\NewContactMessageNotification($contactMessage));
        } catch (\Exception $e) {
            \Log::warning('Failed to notify admins of new contact message: ' . $e->getMessage());
        }

        return redirect()->route('guest.contact')->with('success', 'تم إرسال رسالتك بنجاح! فريقنا رح يتواصل معك بأقرب وقت.');
    }
}
