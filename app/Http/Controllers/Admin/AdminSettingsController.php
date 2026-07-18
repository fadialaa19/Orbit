<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class AdminSettingsController extends Controller
{
    /**
     * عرض صفحة الإعدادات
     */
    public function index()
    {
        // جلب الإعدادات أو إنشاء سجل افتراضي إذا لم يوجد
        $setting = Setting::firstOrCreate(['id' => 1]);
        
        return view('admin.settings', compact('setting'));
    }

    /**
     * تحديث الإعدادات
     */
    /**
     * تحديث الإعدادات
     */
    public function update(Request $request)
    {
        // 1. جلب السجل أولاً أو إنشاؤه لضمان الأمان
        $setting = \App\Models\Setting::firstOrCreate(['id' => 1]);

        // 2. معالجة وضع الصيانة والتوقيت
        $setting->maintenance_mode = $request->has('maintenance_mode');
        
        if ($setting->maintenance_mode && $request->filled('maintenance_until')) {
            $setting->maintenance_until = $request->maintenance_until;
        } else {
            // إذا تم إغلاق وضع الصيانة أو ترك الحقل فارغاً، نلغي المؤقت
            $setting->maintenance_until = null;
        }

        // 3. تحديث النصوص والبيانات الأساسية
        $setting->site_name = $request->site_name ?? $setting->site_name ?? 'Orbit';
        $setting->primary_color = $request->primary_color ?? $setting->primary_color ?? '#6366f1';
        $setting->ai_api_key = $request->ai_api_key;
        $setting->maintenance_message = $request->maintenance_message;

        // بيانات التواصل والروابط الاجتماعية (تظهر بالفوتر وصفحة "تواصل معنا")
        $request->validate([
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'facebook_url' => 'nullable|url|max:500',
            'instagram_url' => 'nullable|url|max:500',
            'whatsapp_url' => 'nullable|url|max:500',
            'telegram_url' => 'nullable|url|max:500',
        ]);
        $setting->contact_email = $request->contact_email;
        $setting->contact_phone = $request->contact_phone;
        $setting->facebook_url = $request->facebook_url;
        $setting->instagram_url = $request->instagram_url;
        $setting->whatsapp_url = $request->whatsapp_url;
        $setting->telegram_url = $request->telegram_url;

        // 4. تحديث مصفوفة بوابات الدفع بشكل آمن ودقيق
        $gateways = $request->payment_gateways ?? [];
        foreach ($gateways as $index => $gateway) {
            // التحقق مما إذا كان حقل الـ active قادماً في الـ request ومحدد كـ checkbox
            // يقبل الحالات: موجود، أو قيمته تساوي 'on' أو 1
            $isActive = isset($gateway['active']) && ($gateway['active'] == 'on' || $gateway['active'] == 1 || $gateway['active'] == true);
            
            $gateways[$index]['name'] = $gateway['name'] ?? 'بوابة دفع';
            $gateways[$index]['account_number'] = $gateway['account_number'] ?? '';
            $gateways[$index]['active'] = $isActive;
        }
        $setting->payment_gateways = $gateways;

        // 5. رفع الصور والشعارات (Logos)
        if ($request->hasFile('logo')) {
            $logoName = 'logo.png';
            // Best-effort cleanup: some S3-compatible backends (e.g. Cloudflare R2)
            // return 403 instead of 404 for a HeadObject on a missing key, which
            // makes exists() throw rather than return false. Just attempt the
            // delete directly and ignore failures.
            try {
                \Storage::disk('public')->delete('logos/'.$logoName);
            } catch (\Throwable $e) {
            }
            $request->file('logo')->storeAs('logos', $logoName, 'public');
            $setting->logo_path = 'logos/' . $logoName;
        }

        if ($request->hasFile('favicon')) {
            $favName = 'favicon.png';
            try {
                \Storage::disk('public')->delete('favicons/'.$favName);
            } catch (\Throwable $e) {
            }
            $request->file('favicon')->storeAs('favicons', $favName, 'public');
            $setting->favicon_path = 'favicons/' . $favName;
        }

        // 6. حفظ التعديلات في قاعدة البيانات ومسح الكاش فوراً
        $setting->save();
        \Illuminate\Support\Facades\Cache::flush();

        return back()->with('success', 'تم تحديث الإعدادات بنجاح وحفظ بوابات الدفع ✨');
    }
}