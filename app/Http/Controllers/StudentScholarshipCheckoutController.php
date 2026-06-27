<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentScholarshipCheckoutController extends Controller
{
    public function show(Scholarship $scholarship)
    {
        if ($scholarship->status !== 'active') {
            abort(404);
        }

        $hasPaid = Order::paid()
            ->where('user_id', Auth::id())
            ->where('scholarship_id', $scholarship->id)
            ->exists();

        if ($hasPaid) {
            return redirect()
                ->route('dashboard.scholarships.show', $scholarship->id)
                ->with('success', 'تم الدفع سابقاً — استمرار التقديم');
        }

        return view('dashboard.scholarships.checkout', compact('scholarship'));
    }

    public function store(Request $request, Scholarship $scholarship)
    {
        // FREE_MODE: تجاوز نموذج/حقول الدفع وإنشاء Order كمدفوع فوراً (احتياطاً في حال تم إرسال POST مباشرة)
        if (config('app.free_mode')) {
            $existing = Order::forUserAndScholarship(Auth::id(), $scholarship->id)
                ->whereIn('status', ['paid', 'pending'])
                ->first();

            if ($existing && $existing->status === 'paid') {
                return redirect()
                    ->route('dashboard.scholarships.show', $scholarship->id)
                    ->with('success', 'تم الدفع سابقاً — استمرار التقديم');
            }

            if (!$existing || $existing->status !== 'paid') {
                // إذا كان pending سابقاً أو لا يوجد طلب: نضمن paid
                if ($existing && $existing->status === 'pending') {
                    $existing->update([
                        'status' => 'paid',
                        'payment_method' => 'free_mode',
                        'transaction_id' => 'free_mode',
                        'bank_name' => null,
                        'transfer_from' => null,
                        'receipt_image' => null,
                        'admin_notes' => 'تم تفعيل الوصول المجاني تلقائياً بسبب FREE_MODE',
                    ]);

                    return redirect()
                        ->route('dashboard.scholarships.show', $scholarship->id)
                        ->with('success', 'تم تفعيل المنحة مجاناً بنجاح');
                }

                Order::create([
                    'user_id' => Auth::id(),
                    'scholarship_id' => $scholarship->id,
                    'amount' => (float) ($scholarship->price ?? 0),
                    'status' => 'paid',
                    'payment_method' => 'free_mode',
                    'transaction_id' => 'free_mode',
                    'bank_name' => null,
                    'transfer_from' => null,
                    'receipt_image' => null,
                    'admin_notes' => 'تم تفعيل الوصول المجاني تلقائياً بسبب FREE_MODE',
                ]);

                return redirect()
                    ->route('dashboard.scholarships.show', $scholarship->id)
                    ->with('success', 'تم تفعيل المنحة مجاناً بنجاح');
            }
        }

        if ($scholarship->status !== 'active') {
            abort(404);
        }

        $existing = Order::forUserAndScholarship(Auth::id(), $scholarship->id)
            ->whereIn('status', ['pending', 'paid'])
            ->first();

        if ($existing && $existing->status === 'paid') {
            return redirect()
                ->route('dashboard.scholarships.show', $scholarship->id)
                ->with('success', 'تم الدفع سابقاً — استمرار التقديم');
        }

        if ($existing && $existing->status === 'pending') {
            return redirect()
                ->route('dashboard.settings')
                ->with('error', 'لديك طلب قيد المراجعة لهذه المنحة');
        }

        $validated = $request->validate([
            'payment_method' => 'required|string|max:50',
            'transaction_id' => 'required|string|max:120',
            'bank_name' => 'required|string|max:120',
            'transfer_from' => 'required|string|max:120',
            'receipt_image' => 'nullable|image|max:5120',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt_image')) {
            $receiptPath = $request->file('receipt_image')->store('receipts', 'public');
        }

        $amount = (float) ($scholarship->price ?? 0);

        $order = Order::create([
            'user_id' => Auth::id(),
            'scholarship_id' => $scholarship->id,
            'amount' => $amount,
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $validated['transaction_id'],
            'bank_name' => $validated['bank_name'],
            'transfer_from' => $validated['transfer_from'],
            'receipt_image' => $receiptPath,
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        return redirect()
            ->route('dashboard.settings')
            ->with('success', 'تم إرسال بيانات الدفع بنجاح. سيتم مراجعة طلبك من الأدمن');
    }

    public function showPaymentForm($id)
{
    $scholarship = Scholarship::findOrFail($id);
    
    // جلب إعدادات المنصة الحالية
    $setting = \App\Models\Setting::first(); 

    return view('dashboard.scholarships.payment', compact('scholarship', 'setting'));
}
}
$query = Scholarship::query();

if ($request->has('countries')) {
    $query->whereIn('country', $request->countries);
}

if ($request->has('funding_type')) {
    // مثال حسب تقسيم قاعدة بياناتك للتمويل
    $query->whereIn('type', $request->funding_type); 
}

