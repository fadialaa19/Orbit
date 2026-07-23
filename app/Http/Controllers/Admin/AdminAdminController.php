<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminAdminController extends Controller
{
    // حساب محمي: لا يقدر أي مدير آخر (حتى مدير عام) يعدّله أو يحذفه أو يغيّر
    // صلاحياته أو حالته - فقط صاحب الحساب نفسه (مسجّل دخول بنفس الإيميل).
    private const PROTECTED_ADMIN_EMAIL = 'fadialaa@gmail.com';

    private function isProtectedFromOthers(User $admin): bool
    {
        return strcasecmp($admin->email, self::PROTECTED_ADMIN_EMAIL) === 0
            && auth()->id() !== $admin->id;
    }

    public function index(Request $request)
    {
        $query = User::whereIn('role', ['super_admin', 'scholarship_admin', 'support_admin']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $admins = $query->latest()->paginate(10);

        $activeAdmins = User::whereIn('role', ['super_admin', 'scholarship_admin', 'support_admin'])->where('status', 'active')->count();
        $inactiveAdmins = User::whereIn('role', ['super_admin', 'scholarship_admin', 'support_admin'])->where('status', 'inactive')->count();

        return view('admin.admins', compact('admins', 'activeAdmins', 'inactiveAdmins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,scholarship_admin,support_admin,custom',
            'permissions' => 'nullable|array',
            'job_title' => 'nullable|string|max:255',
            'team_bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // إذا كان مدير عام، بنعطيه كل الصلاحيات تلقائياً
        $permissions = $request->role === 'super_admin'
            ? ['dashboard', 'scholarships', 'students', 'applications', 'support', 'communities', 'official-documents', 'announcements', 'contacts', 'xp', 'admins']
            : $request->input('permissions', []);

        // "مخصص" مجرد خيار واجهة يعني "لا تطبّق صلاحيات افتراضية جاهزة"، وليس
        // قيمة فعلية مسموح بها في عمود role (enum). التخصيص الحقيقي يظل في
        // مصفوفة permissions أعلاه؛ نخزّن دور حقيقي فقط لعمود role.
        $role = $request->role === 'custom' ? 'scholarship_admin' : $request->role;

        // ✅ تصحيح: تم تغييرها من Admin::create إلى User::create
        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $role,
            'permissions' => $permissions,
            'status' => 'active',
            'job_title' => $request->job_title,
            'team_bio' => $request->team_bio,
            'show_on_team' => $request->boolean('show_on_team'),
            // Admin-created accounts are trusted immediately (no self-registration
            // email-ownership check needed) — otherwise the verified.ensure
            // middleware on ticket-reply routes would lock new managers out.
            'email_verified_at' => now(),
        ]);

        if ($request->hasFile('avatar')) {
            $admin->avatar = $request->file('avatar')->store('avatars', 'public');
            $admin->save();
        }

        return redirect()->back()->with('success', 'تم إضافة المدير وتخصيص صلاحياته بنجاح');
    }

    public function update(Request $request, $id)
    {
        // ✅ تصحيح: تم تغييرها من Admin::findOrFail إلى User::findOrFail
        $admin = User::findOrFail($id);

        if ($this->isProtectedFromOthers($admin)) {
            return redirect()->back()->with('error', 'هذا الحساب محمي - لا يمكن لأي مدير آخر تعديله أو تغيير صلاحياته أو حالته.');
        }

        // إذا كان الطلب فقط لتغيير الحالة (Active/Inactive) من الجدول
        if ($request->has('status') && $request->count() == 5) { 
            $admin->update(['status' => $request->status]);
            return redirect()->back()->with('success', 'تم تغيير حالة المدير بنجاح');
        }

        // أما إذا كان تعديل كامل البيانات والصلاحيات من المودال:
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'role' => 'required|in:super_admin,scholarship_admin,support_admin,custom',
            'permissions' => 'nullable|array',
            'password' => 'nullable|string|min:6',
            'job_title' => 'nullable|string|max:255',
            'team_bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $permissions = $request->role === 'super_admin'
            ? ['dashboard', 'scholarships', 'students', 'applications', 'support', 'communities', 'official-documents', 'announcements', 'contacts', 'xp', 'admins']
            : $request->input('permissions', []);

        // "مخصص" خيار واجهة فقط، مش قيمة صالحة في عمود role (enum) - راجع نفس الشرح في store()
        $role = $request->role === 'custom' ? 'scholarship_admin' : $request->role;

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $role,
            'permissions' => $permissions,
            'job_title' => $request->job_title,
            'team_bio' => $request->team_bio,
            'show_on_team' => $request->boolean('show_on_team'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($admin->getRawOriginal('avatar')) {
                Storage::disk('public')->delete($admin->getRawOriginal('avatar'));
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $admin->update($data);

        return redirect()->back()->with('success', 'تم تحديث بيانات وصلاحيات المدير بنجاح');
    }

    public function destroy(User $admin)
    {
        if (auth()->id() === $admin->id) {
            return redirect()->back()->with('error', 'لا يمكنك حذف حسابك الشخصي!');
        }

        if ($this->isProtectedFromOthers($admin)) {
            return redirect()->back()->with('error', 'هذا الحساب محمي - لا يمكن لأي مدير آخر حذفه.');
        }

        $admin->delete();
        return redirect()->back()->with('success', 'تم حذف المدير بنجاح');
    }
}