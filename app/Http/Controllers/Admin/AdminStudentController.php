<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminStudentController extends Controller
{
    // دالة موحدة لجلب البيانات تمنع أخطاء الـ Undefined Variable
    private function getStudentsData()
    {
        return [
            'students' => User::where('role', 'student')
                ->when(request('search'), function($q) {
                    $q->where('name', 'like', '%'.request('search').'%')
                      ->orWhere('email', 'like', '%'.request('search').'%');
                })
                ->latest()
                ->paginate(12),
            
            'stats' => [
                'total' => User::where('role', 'student')->count(),
                'active' => User::where('role', 'student')->where('status', 'active')->count(),
                'pending' => User::where('role', 'student')->where('status', 'pending')->count(),
                'inactive' => User::where('role', 'student')->where('status', 'inactive')->count(),
            ]
        ];
    }

    public function index()
    {
        return view('admin.students', $this->getStudentsData());
    }

    public function show($id)
    {
        $student = User::where('role', 'student')->with('documents')->findOrFail($id);

        return view('admin.students.show', compact('student'));
    }

    public function updateDocumentStatus(Request $request, $studentId, \App\Models\StudentDocument $document)
    {
        abort_unless((int) $document->user_id === (int) $studentId, 404);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $document->update([
            'status' => $validated['status'],
            'admin_note' => $validated['status'] === 'rejected' ? ($validated['admin_note'] ?? null) : null,
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة المستند بنجاح');
    }

    // ⭐ إضافة هذه الدالة لحل مشكلة الـ Error 500
    public function create()
    {
        return view('admin.students', $this->getStudentsData());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'status' => 'required|in:active,pending,inactive',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
            'role' => 'student',
        ]);

        // استخدم التوجيه لـ index بدلاً من back لضمان تحديث البيانات
        return redirect()->route('admin.students.index')->with('success', 'تم إضافة الطالب بنجاح');
    }
    
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'status' => 'required|in:active,pending,inactive',
            'password' => 'nullable|min:8',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->status = $validated['status'];
        if ($request->filled('phone')) {
            $user->phone = $validated['phone'];
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->back()->with('success', 'تم تحديث بيانات الطالب بنجاح');
    }

    // دالة الحذف (Destroy)
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'تم حذف الطالب بنجاح');
    }

    // تفعيل يدوي للبريد الإلكتروني (لحالات وصول الإيميل متعثراً أو غير مضمون)
    public function verifyEmail($id)
    {
        $user = User::findOrFail($id);
        $user->email_verified_at = now();
        $user->save();

        return redirect()->back()->with('success', 'تم تفعيل حساب الطالب يدوياً بنجاح');
    }
    }