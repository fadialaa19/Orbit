@extends('layouts.app')

@section('title', 'تواصل معنا - Orbit')
@section('meta_description', 'تواصل مع فريق Orbit لأي استفسار أو مساعدة بخصوص المنح الدراسية - نرد عليك بأسرع وقت ممكن.')

@section('content')
<section class="py-20 px-4 md:px-8 max-w-7xl mx-auto">
    <!-- Hero -->
    <div class="text-center mb-20" data-aos="fade-down">
        <span class="bg-gradient-to-r from-gold-600 to-gold-400 text-white px-6 py-3 rounded-[2rem] text-lg font-black inline-flex items-center gap-3 shadow-xl mb-8 mx-auto">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            نحن هون لمساعدتك
        </span>
        <h1 class="text-5xl md:text-6xl font-black text-slate-900 mb-6 leading-tight">تواصل <span class="grad-text">معنا</span></h1>
        <p class="text-xl text-gray-500 font-bold max-w-2xl mx-auto leading-relaxed">عندك سؤال، اقتراح، أو محتاج مساعدة؟ اكتبلنا وفريقنا رح يرد عليك بأقرب وقت ممكن.</p>
    </div>

    @if(session('success'))
        <div data-aos="fade-up" class="max-w-3xl mx-auto mb-10 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-3xl p-5 text-center font-black flex items-center justify-center gap-3">
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 items-start">
        {{-- معلومات التواصل --}}
        <div class="lg:col-span-2 space-y-6" data-aos="fade-left">
            <div class="bg-gradient-to-br from-navy-900 to-navy-950 rounded-[2.5rem] p-8 text-white relative overflow-hidden">
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-gold-500/10 rounded-full"></div>
                <div class="absolute -bottom-16 -right-10 w-52 h-52 bg-white/5 rounded-full"></div>
                <h3 class="font-black text-xl mb-8 relative">معلومات التواصل</h3>

                <div class="space-y-6 relative">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gold-500/20 rounded-2xl flex items-center justify-center text-xl shrink-0">✉️</div>
                        <div>
                            <p class="text-[11px] text-slate-300 font-bold uppercase tracking-widest">البريد الإلكتروني</p>
                            <p class="font-black" dir="ltr">{{ \App\Models\Setting::get('contact_email', 'orbit.ships@gmail.com') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gold-500/20 rounded-2xl flex items-center justify-center text-xl shrink-0">📞</div>
                        <div>
                            <p class="text-[11px] text-slate-300 font-bold uppercase tracking-widest">الهاتف</p>
                            <p class="font-black" dir="ltr">{{ \App\Models\Setting::get('contact_phone', '+970 59 270 4945') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gold-500/20 rounded-2xl flex items-center justify-center text-xl shrink-0">⚡</div>
                        <div>
                            <p class="text-[11px] text-slate-300 font-bold uppercase tracking-widest">وقت الرد المتوقع</p>
                            <p class="font-black">خلال 24 ساعة</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                <h4 class="font-black text-slate-800 mb-4">تحتاج جواب أسرع؟</h4>
                <p class="text-sm text-slate-500 font-bold leading-relaxed mb-5">مساعدنا الذكي متاح على مدار الساعة أسفل الشاشة، بيقدر يجاوبك فوراً عن أي سؤال يخص المنح أو حسابك.</p>
                <a href="{{ route('guest.scholarships') }}" class="inline-flex items-center gap-2 text-gold-600 font-black hover:underline">
                    استكشف المنح الدراسية
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
            </div>
        </div>

        {{-- الفورم --}}
        <div class="lg:col-span-3 bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-slate-50" data-aos="fade-right">
            <form action="{{ route('guest.contact.submit') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">الاسم الكامل</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="أحمد محمد"
                               class="w-full bg-slate-50 border-2 border-transparent focus:border-gold-500 focus:bg-white rounded-2xl py-4 px-5 outline-none transition-all font-medium">
                        @error('name') <p class="mt-1 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">البريد الإلكتروني</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="your.email@example.com" dir="ltr"
                               class="w-full bg-slate-50 border-2 border-transparent focus:border-gold-500 focus:bg-white rounded-2xl py-4 px-5 outline-none transition-all font-medium text-left">
                        @error('email') <p class="mt-1 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">الموضوع</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="مثال: استفسار عن منحة"
                           class="w-full bg-slate-50 border-2 border-transparent focus:border-gold-500 focus:bg-white rounded-2xl py-4 px-5 outline-none transition-all font-medium">
                    @error('subject') <p class="mt-1 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">رسالتك</label>
                    <textarea name="message" required rows="6" placeholder="اكتب رسالتك هنا بالتفصيل..."
                              class="w-full bg-slate-50 border-2 border-transparent focus:border-gold-500 focus:bg-white rounded-2xl py-4 px-5 outline-none transition-all font-medium resize-none">{{ old('message') }}</textarea>
                    @error('message') <p class="mt-1 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-gold-600 to-gold-700 text-white py-5 rounded-[2rem] font-black text-lg shadow-xl hover:shadow-2xl hover:scale-[1.01] transition-all flex items-center justify-center gap-3">
                    إرسال الرسالة
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
