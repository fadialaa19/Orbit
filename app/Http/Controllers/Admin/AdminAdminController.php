<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAdminController extends Controller
{
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
            'role' => 'required|string',
            'permissions' => 'nullable|array'
        ]);

        // إذا كان مدير عام، بنعطيه كل الصلاحيات تلقائياً
        $permissions = $request->role === 'super_admin' 
            ? ['dashboard', 'scholarships', 'students', 'applications', 'support', 'contacts', 'admins'] 
            : $request->input('permissions', []);

        // ✅ تصحيح: تم تغييرها من Admin::create إلى User::create
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'permissions' => $permissions,
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'تم إضافة المدير وتخصيص صلاحياته بنجاح');
    }

    public function update(Request $request, $id)
    {
        // ✅ تصحيح: تم تغييرها من Admin::findOrFail إلى User::findOrFail
        $admin = User::findOrFail($id);

        // إذا كان الطلب فقط لتغيير الحالة (Active/Inactive) من الجدول
        if ($request->has('status') && $request->count() == 5) { 
            $admin->update(['status' => $request->status]);
            return redirect()->back()->with('success', 'تم تغيير حالة المدير بنجاح');
        }

        // أما إذا كان تعديل كامل البيانات والصلاحيات من المودال:
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'role' => 'required|string',
            'permissions' => 'nullable|array',
            'password' => 'nullable|string|min:6',
        ]);

        $permissions = $request->role === 'super_admin'
            ? ['dashboard', 'scholarships', 'students', 'applications', 'support', 'contacts', 'admins']
            : $request->input('permissions', []);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'permissions' => $permissions,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->back()->with('success', 'تم تحديث بيانات وصلاحيات المدير بنجاح');
    }

    public function destroy(User $admin)
    {
        if (auth()->id() === $admin->id) {
            return redirect()->back()->with('error', 'لا يمكنك حذف حسابك الشخصي!');
        }

        $admin->delete();
        return redirect()->back()->with('success', 'تم حذف المدير بنجاح');
    }
}