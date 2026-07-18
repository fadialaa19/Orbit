<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\XpTransaction;
use App\Services\XpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminXpController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');

        $students = User::where('role', 'student')
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            }))
            ->orderByDesc('xp')
            ->paginate(15)
            ->withQueryString();

        $recentTransactions = XpTransaction::with(['user:id,name', 'admin:id,name'])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.xp', compact('students', 'search', 'recentTransactions'));
    }

    public function adjust(Request $request, User $student, XpService $xpService)
    {
        abort_unless($student->role === 'student', 404);

        $validated = $request->validate([
            'amount' => 'required|integer|min:-100000|max:100000|not_in:0',
            'reason' => 'required|string|max:255',
        ]);

        $xpService->award($student, (int) $validated['amount'], $validated['reason'], Auth::user());

        return back()->with('success', 'تم تعديل رصيد نقاط الطالب بنجاح');
    }
}
