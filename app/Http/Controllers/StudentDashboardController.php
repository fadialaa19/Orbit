<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentDashboardController extends Controller
{
    public function student()
    {
        $student = Auth::user();
        
        $profileCompletion = $student->calculateProfileCompletion();
        
        // جلب الإحصائيات الحقيقية من قاعدة البيانات
        $favoritesCount = $student->favoriteScholarships()->count();
        
        // الطلبات النشطة (pending)
        $applicationsCount = $student->orders()->where('status', 'pending')->count();
        
        // الطلبات المكتملة (paid)
        $completedCount = $student->orders()->where('status', 'paid')->count();
        
        // قيد المراجعة (pending_review)
        $reviewCount = $student->orders()->where('status', 'pending_review')->count();
        
        $stats = [
            'favorites' => $favoritesCount,
            'applications' => $applicationsCount,
            'completed' => $completedCount,
            'review' => $reviewCount,
        ];

        // جلب المنح الموصى بها
        $recommended_scholarships = Scholarship::active()
            ->whereDoesntHave('favoritedBy', function ($query) use ($student) {
                $query->where('user_id', $student->id);
            })
            ->inRandomOrder()
            ->take(3)
            ->get();

        // جلب آخر الأنشطة من Orders
        $activities = $student->orders()
            ->with('scholarship')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'type' => $order->status === 'paid' ? 'completed' : 'application',
                    'title' => $order->scholarship->title_ar ?? 'المنحة',
                    'time' => $order->created_at->diffForHumans(),
                ];
            });

        // قائمة المهام: محسوبة من بيانات حقيقية موجودة على حساب الطالب
        $requiredDocs = $student->required_documents ?? [];
        $requiredDocsComplete = collect(['passport', 'national_id', 'high_school_cert', 'birth_cert', 'cv'])
            ->every(fn ($key) => !empty($requiredDocs[$key] ?? null));
        $hasApplication = ($applicationsCount + $completedCount) > 0;

        $tasks = [
            ['title' => 'إكمال الملف الشخصي', 'completed' => $profileCompletion >= 100, 'link' => route('dashboard.profile')],
            ['title' => 'رفع المستندات المطلوبة', 'completed' => $requiredDocsComplete, 'link' => route('dashboard.profile')],
            ['title' => 'حفظ أول منحة في المفضلة', 'completed' => $favoritesCount > 0, 'link' => route('dashboard.scholarships')],
            ['title' => 'تقديم أول طلب منحة', 'completed' => $hasApplication, 'link' => route('dashboard.scholarships')],
        ];

        // الإنجازات: محسوبة من نفس البيانات الحقيقية بدون أي جدول جديد
        $optionalDocs = $student->optional_documents ?? [];
        $hasLanguageCert = !empty($optionalDocs['language_cert'] ?? null);
        $currentLevel = (int) floor(($student->xp ?? 0) / 1000) + 1;

        $badges = [
            ['icon' => '🏆', 'name' => 'البداية القوية', 'description' => 'إنشاء الحساب وتأكيد البريد الإلكتروني', 'unlocked' => true],
            ['icon' => '⭐', 'name' => 'الملف الذهبي', 'description' => 'إكمال ملفك الشخصي بنسبة 100%', 'unlocked' => $profileCompletion >= 100],
            ['icon' => '🎓', 'name' => 'المثقف الأديب', 'description' => 'رفع شهادة لغة معتمدة بالملف', 'unlocked' => $hasLanguageCert],
            ['icon' => '⚡', 'name' => 'التقديم الأول', 'description' => 'إرسال أول طلب منحة دراسية بنجاح', 'unlocked' => $hasApplication],
            ['icon' => '💎', 'name' => 'المفضلة', 'description' => 'حفظ أول منحة في قائمة المفضلة', 'unlocked' => $favoritesCount > 0],
            ['icon' => '🚀', 'name' => 'طموح لا ينتهي', 'description' => 'الوصول إلى المستوى الخامس في المنصة', 'unlocked' => $currentLevel >= 5],
        ];

        return view('dashboard.student', compact('student', 'stats', 'recommended_scholarships', 'activities', 'profileCompletion', 'tasks', 'badges'));
    }

    public function scholarships(Request $request)
    {
        $search = $request->get('q');
        $categories = $request->get('category'); // مصفوفة المستويات الأكاديمية القادمة من الـ Blade الجديد
        $coverages = $request->get('coverage');  // مصفوفة مزايا التمويل (JSON Array) القادمة من الـ Blade الجديد

        // الفلترة التلقائية الذكية بالاعتماد على معدل الثانوية الخاصة بالطالب إن وُجد
        $studentHighSchoolGpa = null;
        if (Auth::check()) {
            $studentHighSchoolGpa = Auth::user()->high_school_gpa;
            $studentHighSchoolGpa = is_numeric($studentHighSchoolGpa) ? (float)$studentHighSchoolGpa : null;
        }

        $query = Scholarship::active()
    ->where(function($q) {
        $q->where('deadline', '>=', now()->startOfDay())
          ->orWhereNull('deadline');
    })
            // 1. البحث النصي المشترك
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('title_ar', 'like', "%{$search}%")
                      ->orWhere('title_en', 'like', "%{$search}%")
                      ->orWhere('country', 'like', "%{$search}%")
                      ->orWhere('university', 'like', "%{$search}%")
                      ->orWhereJsonContains('tags', $search);
                });
            })
            // 2. فلتر المستوى الأكاديمي المتعدد المحدث (category) المتوافق مع الـ Blade وقاعدة البيانات
            ->when($categories, function($q) use ($categories) {
                return $q->whereIn('category', (array)$categories);
            })
            // 3. فلتر مزايا التمويل الذكي المحدث ليتعامل مع حقول الـ JSON Array (coverage) بدقة
            ->when($coverages, function($q) use ($coverages) {
                return $q->where(function($subQuery) use ($coverages) {
                    foreach ((array)$coverages as $coverItem) {
                        $subQuery->orWhereJsonContains('coverage', $coverItem);
                    }
                });
            })
            // 4. الفلترة التلقائية الذكية حسب معدل الثانوية العامة
            ->when(!is_null($studentHighSchoolGpa), function ($q) use ($studentHighSchoolGpa) {
                if ($studentHighSchoolGpa >= 90) {
                    return $q->where(function ($qq) {
                        $qq->whereJsonContains('tags', '>=90')
                           ->orWhereJsonContains('tags', 'high_gpa');
                    });
                }
                return $q->where(function ($qq) {
                    $qq->whereJsonContains('tags', '<90')
                       ->orWhereJsonContains('tags', 'low_gpa');
                });
            });

        // ترتيب المنح بالتاريخ الأحدث وتحديد عدد العناصر في الصفحة
        $scholarships = $query->latest()->paginate(12);
        
        // تثبيت البارامترات في روابط الترقيم السفلي لمنع اختفاء الفلاتر عند التنقل بين الصفحات
        $scholarships->appends($request->query());

        return view('dashboard.scholarships', compact('scholarships', 'search'));
    }
    

    public function show(Scholarship $scholarship)
    {
        if ($scholarship->status !== 'active') {
            abort(404);
        }

        $match_percent = rand(85, 98);
        $recommended_tags = $scholarship->recommended_tags ?? ['ممولة كاملاً', 'ماجستير', 'علوم الحاسب'];

        $hasPaid = false;
        $isFavorited = false;
        if (Auth::check()) {
            $hasPaid = \App\Models\Order::paid()
                ->where('user_id', Auth::id())
                ->where('scholarship_id', $scholarship->id)
                ->exists();
            $isFavorited = $scholarship->favoritedBy()->where('user_id', Auth::id())->exists();
        }

        return view('dashboard.show', compact('scholarship', 'match_percent', 'recommended_tags', 'hasPaid', 'isFavorited'));
    }

    public function applications()
    {
        $student = Auth::user();
        $applications = $student->orders()
            ->with('scholarship')
            ->latest()
            ->paginate(10);
            
        return view('dashboard.applications', compact('applications'));
    }

    public function favorites()
{
    $student = Auth::user();
    
    // جلب المفضلات مع التأكد من تحويل قيم اللوجو والبيانات لتطابق فرونت آند Alpine
    $favorites = $student->favoriteScholarships()->get()->map(function($scholarship) {
        return [
            'id' => $scholarship->id,
            'title' => $scholarship->title_ar ?? $scholarship->title_en,
            'category' => $scholarship->category ?? 'منحة دراسية',
            // $scholarship->logo_image يمر أصلاً عبر accessor الموديل الذي يبني الرابط الكامل
            'logo_image' => $scholarship->logo_image,
            'financial_value' => $scholarship->financial_value,
            'amount' => $scholarship->price ? ('$' . number_format((float) $scholarship->price, 0)) : '$50,000',
            'funding' => $scholarship->financial_value ?: 'ممولة بالكامل',
        ];
    });

    return view('dashboard.favorites', compact('favorites'));
}

    public function profile()
    {
        $user = Auth::user();
        $profileCompletion = $user->calculateProfileCompletion();
        return view('dashboard.profile', compact('user', 'profileCompletion'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'phone' => 'nullable|string|max:20',
            'birthdate' => 'nullable|date',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'national_id' => 'nullable|string|max:50',
            'passport_number' => 'nullable|string|max:50',
            'passport_expiry' => 'nullable|date',
            'passport_country' => 'nullable|string|max:100',
            'high_school_name' => 'nullable|string|max:200',
            'high_school_country' => 'nullable|string|max:100',
            'high_school_year' => 'nullable|integer|min:1950|max:2030',
            'high_school_gpa' => 'nullable|numeric|min:0|max:4',
            'high_school_branch' => 'nullable|string|max:100',
            'diploma_institute' => 'nullable|string|max:200',
            'diploma_country' => 'nullable|string|max:100',
            'diploma_year' => 'nullable|integer|min:1950|max:2030',
            'diploma_degree' => 'nullable|string|max:200',
            'diploma_gpa' => 'nullable|numeric|min:0|max:4',
            'bachelor_university' => 'nullable|string|max:200',
            'bachelor_country' => 'nullable|string|max:100',
            'bachelor_year' => 'nullable|integer|min:1950|max:2030',
            'bachelor_degree' => 'nullable|string|max:200',
            'bachelor_gpa' => 'nullable|numeric|min:0|max:4',
            'master_university' => 'nullable|string|max:200',
            'master_country' => 'nullable|string|max:100',
            'master_year' => 'nullable|integer|min:1950|max:2030',
            'master_degree' => 'nullable|string|max:200',
            'master_gpa' => 'nullable|numeric|min:0|max:4',
            'languages' => 'nullable|array',
            'languages.*.name' => 'string|max:50',
            'languages.*.cert' => 'string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'required_documents' => 'nullable|array',
            'optional_documents' => 'nullable|array',
            'docs' => 'nullable|array', 
            'docs.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ];

        $validated = $request->validate($rules);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $reqDocs = $user->required_documents ?? [];
        $optDocs = $user->optional_documents ?? [];
        $legacyDocs = $user->documents ?? [];

        // نفس أسماء المفاتيح المستخدمة في resources/views/dashboard/profile.blade.php
        // و User::calculateProfileCompletion() لتمييز أي حقل "docs[key]" إلزامي وأيها اختياري
        $requiredDocKeys = ['passport', 'national_id', 'high_school_cert', 'birth_cert', 'cv'];
        $optionalDocKeys = ['language_cert', 'courses_cert', 'recommendation', 'intent_letter'];

        if ($request->hasFile('docs')) {
            foreach ($request->file('docs') as $key => $file) {
                if ($file && $file->isValid()) {
                    $isOptional = in_array($key, $optionalDocKeys);
                    $docArray = $isOptional ? $optDocs : $reqDocs;

                    $oldPath = $docArray[$key] ?? $legacyDocs[$key] ?? null;
                    if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }

                    $storedPath = $file->store('documents', 'public');
                    if ($isOptional) {
                        $optDocs[$key] = $storedPath;
                    } else {
                        $reqDocs[$key] = $storedPath;
                    }
                }
            }
        }

        $user->required_documents = $reqDocs;
        $user->optional_documents = $optDocs;
        $user->documents = array_merge($legacyDocs, $reqDocs, $optDocs);

        $user->fill($validated);
        $user->profile_completion = $user->calculateProfileCompletion();
        $user->save();

        return redirect()->route('dashboard.profile')->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    public function settings()
    {
        $user = Auth::user();
        return view('dashboard.settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $formType = $request->input('form_type');

        if ($formType === 'profile') {
            $request->validate([
                'name' => 'required|string|max:255',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'current_password' => 'nullable|required_with:new_password|string',
                'new_password' => 'nullable|string|min:8|confirmed',
            ]);

            if ($request->filled('new_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return back()->with('error', 'كلمة المرور الحالية غير صحيحة');
                }
                $user->password = $request->new_password;
            }

            $user->name = $request->name;

            if ($request->hasFile('avatar')) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = $request->file('avatar')->store('avatars', 'public');
            }

            $user->save();

            return back()->with('success', 'تم تحديث الحساب بنجاح');
        }

        if ($formType === 'privacy') {
            $preferences = $user->preferences ?? [];
            $preferences['profile_visible_to_scholarships'] = $request->boolean('profile_visible_to_scholarships');
            $preferences['receive_university_messages'] = $request->boolean('receive_university_messages');
            $user->preferences = $preferences;
            $user->save();

            return back()->with('success', 'تم حفظ إعدادات الخصوصية بنجاح');
        }

        if ($formType === 'notifications') {
            $preferences = $user->preferences ?? [];
            $preferences['notify_new_scholarships'] = $request->boolean('notify_new_scholarships');
            $user->preferences = $preferences;
            $user->save();

            return back()->with('success', 'تم حفظ تفضيلات الإشعارات بنجاح');
        }

        return back();
    }

    public function notifications()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(20);
        return view('dashboard.notifications', compact('notifications'));
    }

    public function services()
    {
        return view('dashboard.services');
    }

    public function toggleFavorite(Scholarship $scholarship)
    {
        $user = Auth::user();
        
        $exists = $user->favoriteScholarships()->where('scholarship_id', $scholarship->id)->exists();
        
        if ($exists) {
            $user->favoriteScholarships()->detach($scholarship->id);
            return back()->with('success', 'تمت إزالة المنحة من المفضلة');
        }
        
        $user->favoriteScholarships()->attach($scholarship->id);
        return back()->with('success', 'تمت إضافة المنحة إلى المفضلة');
    }

    public function ticketShow(SupportTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }
        return view('dashboard.ticket-chat', compact('ticket'));
    }

    public function communications(Request $request)
    {
        return app(\App\Http\Controllers\CommunicationsController::class)->index($request);
    }
}