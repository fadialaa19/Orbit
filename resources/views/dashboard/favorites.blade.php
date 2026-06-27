@extends('layouts.dashboard')
@section('title', 'المفضلات')

@section('content')
<div class="bg-slate-50 min-h-screen py-10 px-4 md:px-10" dir="rtl" x-data="favoritesHub({{ json_encode($favorites) }})" x-init="init()" x-cloak>
    <div class="max-w-7xl mx-auto">

        {{-- الهيدر المحدث بعد حذف شريط المطابقة بالكامل --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div>
                <h1 class="text-3xl font-black text-slate-800 flex items-center gap-3">
                    المفضلات <span class="text-2xl text-indigo-600">(<span x-text="stats.count">0</span>)</span>
                </h1>
                <p class="text-slate-500 font-bold mt-2">المنح التي اخترتها للمتابعة لاحقاً</p>
            </div>
        </div>

        {{-- تأثير التحميل المبدئي Skeleton Loading --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" x-show="loading">
            <template x-for="n in 6" :key="n">
                <div class="h-56 bg-white rounded-[2.5rem] border border-slate-100 shadow-sm animate-pulse"></div>
            </template>
        </div>

        {{-- عرض الكروت بعد انتهاء التحميل --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" x-show="!loading">
            <template x-if="favorites.length === 0">
                <div class="md:col-span-3 text-center py-16">
                    <div class="text-6xl opacity-20">❤️</div>
                    <p class="text-slate-400 font-bold mt-3">لا توجد مفضلات حالياً</p>
                </div>
            </template>

            <template x-for="f in favorites" :key="f.id">
                <div class="bg-white rounded-[2.5rem] p-6 shadow-sm border border-slate-100 hover:shadow-md hover:border-indigo-200 transition-all duration-300 group flex flex-col justify-between">
                    
                    <div class="flex items-start gap-4 mb-4">
                        {{-- 1. صندوق اللوجو الموحد والمطابق تماماً والمحلول برمجياً --}}
                        <div class="w-20 h-20 md:w-24 md:h-24 flex-shrink-0 rounded-2xl overflow-hidden border border-slate-100 bg-slate-50 flex items-center justify-center p-2 relative shadow-inner">
                            
                            {{-- يعرض الصورة فقط إذا كان الحقل يحتوي على قيمة من الداتابيز --}}
                            <img x-show="f.logo_image" :src="f.logo_image" :alt="f.title" class="w-full h-full object-contain" x-cloak>

                            {{-- يعرض الأيقونة الافتراضية فقط إذا كان الحقل فارغاً --}}
                            <div x-show="!f.logo_image" class="w-full h-full bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600" x-cloak>
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.174L11.25 15.89c.445.365 1.055.365 1.5 0l6.99-5.717m-.003 4.31v4.454a2.25 2.25 0 01-2.247 2.247H6.75a2.25 2.25 0 01-2.247-2.247v-4.454m15.122-4.31L12 3l-8.12 6.634m16.24 0l-1.92 11.52H5.8l-1.92-11.52z"/>
                                </svg>
                            </div>

                        </div>

                        {{-- تفاصيل المنحة النصية --}}
                        <div class="flex-1 min-w-0 text-right">
                            <h4 class="font-black text-slate-800 text-lg mb-1 truncate" x-text="f.title"></h4>
                            <p class="text-sm text-slate-500 font-bold mb-2" x-text="f.category"></p>
                            <div class="flex flex-wrap gap-1">
                                <span class="bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-lg text-[10px] font-black" x-text="f.financial_value ?? f.amount"></span>
                                <span class="bg-purple-50 text-purple-600 px-2.5 py-1 rounded-lg text-[10px] font-black" x-text="f.funding ?? 'ممولة بالكامل'"></span>
                            </div>
                        </div>

                        {{-- زر الحذف المفضلة العلوي الأنيق --}}
                        <button class="text-slate-300 hover:text-red-500 transition group-hover:scale-110 flex-shrink-0" title="إزالة من المفضلة" type="button" @click="toggleRemove(f.id)">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    {{-- أزرار التحكم السفلية المربوطة بالـ ID الديناميكي للمنحة --}}
                    <div class="flex gap-3 pt-4 border-t border-slate-100 mt-auto">
                        <a :href="'/dashboard/scholarships/' + f.id" class="flex-1 bg-indigo-600 text-white py-3 px-6 rounded-xl font-black text-sm text-center hover:bg-indigo-700 transition shadow-md shadow-indigo-100">عرض التفاصيل</a>
                        
                        <button class="p-3 text-red-500 bg-red-50 rounded-xl hover:bg-red-100 transition" type="button" @click="toggleRemove(f.id)" aria-label="favorite">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- زر تحميل المزيد --}}
        <div class="text-center mt-16" x-show="!loading && favorites.length > 0">
            <button class="text-slate-400 font-bold text-base hover:text-indigo-600 transition" type="button" @click="loadMore">تحميل المزيد...</button>
        </div>
    </div>

    <script>
        function favoritesHub(initialFavorites) {
            return {
                loading: true,
                favorites: initialFavorites || [],
                stats: { count: 0 },
                page: 1,

                init() {
                    // جعل البيانات تظهر فوراً من مصفوفة الـ Controller ثم إغلاق مؤشر الـ Loading
                    this.stats.count = this.favorites.length;
                    this.loading = false;
                },

                async loadMore() {
                    // في حال رغبت لاحقاً بعمل باجينيشن ديناميكي
                },

                async toggleRemove(id) {
                    this.favorites = this.favorites.filter(f => f.id !== id);
                    this.stats.count = this.favorites.length;
                    
                    try {
                        await fetch(`/dashboard/scholarships/${id}/favorite`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            }
                        });
                    } catch (e) {
                        console.error('Error removing favorite from database:', e);
                    }
                }
            };
        }
    </script>
</div>
@endsection