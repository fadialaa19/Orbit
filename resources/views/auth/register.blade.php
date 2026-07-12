<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-H7HBHJX5PF"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-H7HBHJX5PF');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/png">
    <title>إنشاء حساب جديد - Orbit</title>
    @vite(['resources/css/app.css'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    @include('layouts.partials._brand-styles')
    <style>
        body { font-family: 'Cairo', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-xl w-full" 
         x-data="{ 
            step: 1, 
            name: '{{ old('name', '') }}', 
            email: '{{ old('email', '') }}', 
            country: '{{ old('country', '') }}', 
            field: '{{ old('field', '') }}',
            password: '',
            password_confirmation: '',
            errors: {},

            serverErrors: @js($errors->toArray()),

            init() {
                // Stay on step with errors
                if (this.hasStepErrors(1)) this.step = 1;
                else if (this.hasStepErrors(2)) this.step = 2;
                else if (this.step === 3) this.step = 3;
            },

            hasStepErrors(stepNum) {
                if (stepNum === 1) {
                    return this.serverErrors.name || this.serverErrors.email || this.serverErrors.password || this.serverErrors.password_confirmation;
                }
                if (stepNum === 2) {
                    return this.serverErrors.country;
                }
                return false;
            },

            validateStep1() {
                this.errors = {};
                let valid = true;

                if (!this.name || this.name.trim().length < 3) {
                    this.errors.name = 'الاسم مطلوب (3 أحرف على الأقل).';
                    valid = false;
                }
                if (!this.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
                    this.errors.email = 'البريد الإلكتروني غير صالح.';
                    valid = false;
                }
                if (!this.password || this.password.length < 8) {
                    this.errors.password = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.';
                    valid = false;
                }
                if (this.password !== this.password_confirmation) {
                    this.errors.password_confirmation = 'كلمة المرور غير متطابقة.';
                    valid = false;
                }

                return valid;
            },

            goNext() {
                if (this.step === 1 && !this.validateStep1()) return;
                this.errors = {};
                this.step++;
            },

            goBack() {
                this.step--;
                this.errors = {};
            }
         }">
        
        <div class="flex justify-end mb-4">
            <a href="/" class="flex items-center gap-2 text-slate-500 hover:text-gold-600 font-bold transition">
                <span>العودة للرئيسية</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 md:p-12 border border-slate-50 relative">
            
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 flex items-center justify-center">
                    <img src="{{ asset('assets/images/logo.png') }}" class="w-16 h-16 object-contain" alt="Logo">
                </div>
            </div>

            <h1 class="text-2xl font-black text-center text-slate-800">إنشاء حساب جديد</h1>
            <p class="text-center text-slate-400 mb-6 font-bold text-sm">خطوة <span x-text="step"></span> من 3</p>

            <div class="w-full bg-slate-100 h-2 rounded-full mb-10 flex overflow-hidden">
                <div class="grad-bg h-full transition-all duration-500" :style="'width: ' + (step * 33.33) + '%'"></div>
            </div>

            @php
                $step1Error = $errors->has('name') || $errors->has('email') || $errors->has('password') || $errors->has('password_confirmation');
                $step2Error = $errors->has('country');
            @endphp

            <form action="{{ route('register') }}" method="POST" @submit="if (step < 3) { $event.preventDefault(); goNext(); }">
                @csrf

                <!-- STEP 1 -->
                <div x-show="step === 1" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10">
                    <h2 class="font-black text-slate-800 mb-6 border-r-4 border-gold-600 pr-3">المعلومات الأساسية</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">الاسم الكامل</label>
                            <input type="text" name="name" x-model="name" placeholder="أحمد محمد" 
                                class="w-full bg-slate-50 border-2 rounded-xl py-3 px-5 outline-none transition-all"
                                :class="(errors.name || serverErrors.name) ? 'border-rose-500' : 'border-transparent focus:border-gold-500 focus:bg-white'">
                            <p x-show="errors.name" x-text="errors.name" class="mt-1 text-sm text-rose-500 font-medium" x-cloak></p>
                            @error('name')<p class="mt-1 text-sm text-rose-500 font-medium">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">البريد الإلكتروني</label>
                            <input type="email" name="email" x-model="email" placeholder="your.email@example.com" 
                                class="w-full bg-slate-50 border-2 rounded-xl py-3 px-5 outline-none transition-all text-left" dir="ltr"
                                :class="(errors.email || serverErrors.email) ? 'border-rose-500' : 'border-transparent focus:border-gold-500 focus:bg-white'">
                            <p x-show="errors.email" x-text="errors.email" class="mt-1 text-sm text-rose-500 font-medium" x-cloak></p>
                            @error('email')<p class="mt-1 text-sm text-rose-500 font-medium">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">كلمة المرور</label>
                                <input type="password" name="password" x-model="password" placeholder="••••••••" 
                                    class="w-full bg-slate-50 border-2 rounded-xl py-3 px-5 outline-none transition-all text-left" dir="ltr"
                                    :class="(errors.password || serverErrors.password) ? 'border-rose-500' : 'border-transparent focus:border-gold-500 focus:bg-white'">
                                <p x-show="errors.password" x-text="errors.password" class="mt-1 text-sm text-rose-500 font-medium" x-cloak></p>
                                @error('password')<p class="mt-1 text-sm text-rose-500 font-medium">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">تأكيد كلمة المرور</label>
                                <input type="password" name="password_confirmation" x-model="password_confirmation" placeholder="••••••••" 
                                    class="w-full bg-slate-50 border-2 rounded-xl py-3 px-5 outline-none transition-all text-left" dir="ltr"
                                    :class="(errors.password_confirmation || serverErrors.password_confirmation) ? 'border-rose-500' : 'border-transparent focus:border-gold-500 focus:bg-white'">
                                <p x-show="errors.password_confirmation" x-text="errors.password_confirmation" class="mt-1 text-sm text-rose-500 font-medium" x-cloak></p>
                                @error('password_confirmation')<p class="mt-1 text-sm text-rose-500 font-medium">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-10">
                        <button type="button" @click="goNext()" class="w-full grad-bg text-white py-4 rounded-2xl font-black shadow-xl shadow-gold-100 hover:scale-[1.02] transition">
                            التالي
                        </button>
                    </div>
                </div>

                <!-- STEP 2 -->
                <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10">
                    <h2 class="font-black text-slate-800 mb-6 border-r-4 border-gold-600 pr-3">التفاصيل الأكاديمية</h2>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">البلد</label>
                            <input type="text" name="country" x-model="country" placeholder="السعودية" 
                                class="w-full bg-slate-50 border-2 rounded-xl py-3 px-5 outline-none transition-all"
                                :class="(errors.country || serverErrors.country) ? 'border-rose-500' : 'border-transparent focus:border-gold-500 focus:bg-white'">
                            <p x-show="errors.country" x-text="errors.country" class="mt-1 text-sm text-rose-500 font-medium" x-cloak></p>
                            @error('country')<p class="mt-1 text-sm text-rose-500 font-medium">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">مجال الدراسة</label>
                            <input type="text" name="field" x-model="field" placeholder="علوم الحاسب" 
                                class="w-full bg-slate-50 border-2 border-transparent focus:border-gold-500 focus:bg-white rounded-xl py-3 px-5 outline-none transition-all">
                        </div>
                    </div>

                    <div class="mt-10 flex gap-4">
                        <button type="button" @click="goBack()" class="flex-1 border-2 border-slate-100 py-4 rounded-2xl font-black text-slate-500 hover:bg-slate-50 transition">
                            السابق
                        </button>
                        <button type="button" @click="goNext()" class="flex-[2] grad-bg text-white py-4 rounded-2xl font-black shadow-xl shadow-gold-100 hover:scale-[1.02] transition">
                            التالي
                        </button>
                    </div>
                </div>

                <!-- STEP 3 -->
                <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10">
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h2 class="font-black text-2xl text-slate-800">جاهز للبدء!</h2>
                        <p class="text-slate-400">راجع معلوماتك وابدأ رحلتك الدراسية</p>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-6 space-y-3 mb-8">
                        <div class="flex justify-between border-b border-white pb-2">
                            <span class="text-slate-400">الاسم:</span>
                            <span class="font-bold text-slate-700" x-text="name || 'غير محدد'"></span>
                        </div>
                        <div class="flex justify-between border-b border-white pb-2">
                            <span class="text-slate-400">البريد:</span>
                            <span class="font-bold text-slate-700" x-text="email || 'غير محدد'"></span>
                        </div>
                        <div class="flex justify-between border-b border-white pb-2">
                            <span class="text-slate-400">البلد:</span>
                            <span class="font-bold text-slate-700" x-text="country || 'غير محدد'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">المجال:</span>
                            <span class="font-bold text-slate-700" x-text="field || 'غير محدد'"></span>
                        </div>
                    </div>

                    <div class="mt-10 flex gap-4">
                        <button type="button" @click="goBack()" class="flex-1 border-2 border-slate-100 py-4 rounded-2xl font-black text-slate-500 hover:bg-slate-50 transition">
                            السابق
                        </button>
                        <button type="submit" class="flex-[2] grad-bg text-white py-4 rounded-2xl font-black shadow-xl shadow-gold-100 hover:scale-[1.02] transition">
                            إنهاء التسجيل
                        </button>
                    </div>
                </div>
            </form>

            <p class="text-center mt-8 text-slate-500 font-bold text-sm">
                لديك حساب بالفعل؟ 
                <a href="{{ route('login') }}" class="text-gold-600 hover:underline">تسجيل الدخول</a>
            </p>
        </div>
    </div>

</body>
</html>

