@extends('layouts.admin')

@section('title', 'إدارة المدراء')
@section('breadcrumb', 'المدراء والصلاحيات')

@section('content')
{{-- منع وميض عناصر Alpine --}}
<style>[x-cloak] { display: none !important; }</style>

<div x-data="{ 
    addModal: false, 
    editModal: false, 
    currentAdmin: {},
    
    // 1️⃣ تم تحديث الصفحات لتطابق الباك إند تماماً (7 صفحات)
    systemPages: [
        { key: 'dashboard', name: 'الرئيسية والإحصائيات', icon: '📊', color: 'indigo' },
        { key: 'scholarships', name: 'إدارة المنح الدراسية', icon: '🎓', color: 'emerald' },
        { key: 'students', name: 'إدارة الطلاب', icon: '👨‍🎓', color: 'blue' },
        { key: 'applications', name: 'طلبات التقديم والعمليات', icon: '📥', color: 'amber' },
        { key: 'support', name: 'الدعم الفني والرسائل', icon: '💬', color: 'purple' },
        { key: 'contacts', name: 'رسائل اتصل بنا والآراء', icon: '✉️', color: 'rose' },
        { key: 'admins', name: 'إدارة المدراء والصلاحيات والإعدادات', icon: '🔐', color: 'slate' }
    ],
    
    // 2️⃣ تصحيح المسميات الافتراضية للأدوار لتطابق الـ keys بالكتب تماماً
    roleDefaults: {
        super_admin: ['dashboard', 'scholarships', 'students', 'applications', 'support', 'contacts', 'admins'],
        scholarship_admin: ['dashboard', 'scholarships', 'students', 'applications'],
        support_admin: ['dashboard', 'support', 'contacts']
    },
    
    // الصلاحيات المحددة تلقائياً عند فتح الواجهة لأول مرة (أو المحتفظ بها بعد فشل التحقق)
    addRole: @js(old('form_name') === 'add' ? old('role', 'super_admin') : 'super_admin'),
    addPermissions: @js(old('form_name') === 'add' ? old('permissions', ['dashboard', 'scholarships', 'students', 'applications', 'support', 'contacts', 'admins']) : ['dashboard', 'scholarships', 'students', 'applications', 'support', 'contacts', 'admins']),
    
    // دالة لتحديث الصلاحيات تلقائياً عند تغيير الدور في الإضافة
    updateAddPermissions() {
        this.addPermissions = this.roleDefaults[this.addRole] ? [...this.roleDefaults[this.addRole]] : [];
    }
}" x-init="
    addModal = {{ $errors->any() && old('form_name') === 'add' ? 'true' : 'false' }};
    @if($errors->any() && old('form_name') === 'edit')
        editModal = true;
        currentAdmin = {
            id: {{ (int) old('id') }},
            name: @js(old('name')),
            email: @js(old('email')),
            role: @js(old('role')),
            permissions: @js(old('permissions', []))
        };
    @endif
" class="max-w-full mx-auto space-y-6">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="fixed top-5 left-1/2 -translate-x-1/2 z-[100] min-w-[300px] p-4 bg-emerald-500 text-white rounded-2xl font-black text-sm shadow-2xl flex items-center justify-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="fixed top-5 left-1/2 -translate-x-1/2 z-[100] min-w-[300px] p-4 bg-rose-500 text-white rounded-2xl font-black text-sm shadow-2xl flex items-center justify-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border-2 border-rose-200 text-rose-700 rounded-2xl p-4 font-bold text-sm space-y-1">
            <p class="font-black">تعذّر حفظ البيانات، الرجاء تصحيح الآتي:</p>
            <ul class="list-disc mr-5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">المدراء والصلاحيات</h1>
            <p class="text-xs font-bold text-slate-400 mt-1">التحكم في وصول الموظفين وتخصيص صفحات العمل بدقة</p>
        </div>
        <div class="flex gap-2">
            <button @click="addModal = true" class="bg-gold-600 text-white px-5 py-2.5 rounded-xl font-black text-xs hover:bg-gold-700 shadow-lg shadow-gold-100 transition-all">
                + إضافة مدير جديد وصلاحياته
            </button>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/30">
            <div class="flex items-center gap-4">
                <h3 class="text-sm font-black text-slate-800">قائمة الفريق <span class="text-gold-500 mr-1">({{ $admins->total() }})</span></h3>
                <div class="h-4 w-[1px] bg-slate-200"></div>
                <div class="flex gap-2">
                    <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">{{ $activeAdmins }} نشط</span>
                    <span class="text-[10px] font-black text-slate-400 bg-white border border-slate-100 px-2 py-0.5 rounded-md">{{ $inactiveAdmins }} معطل</span>
                </div>
            </div>
            <form action="{{ route('admin.admins.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث عن مدير..." class="bg-white border border-slate-200 rounded-xl py-2 pr-10 pl-4 text-xs font-bold focus:border-gold-500 outline-none w-full md:w-64">
                <svg class="w-4 h-4 text-slate-400 absolute right-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">الهوية</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">الدور الوظيفي</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">الصفحات المسموحة</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">الحالة</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($admins as $admin)
                    <tr class="hover:bg-slate-50/80 transition-all group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=6366f1&color=fff" class="w-10 h-10 rounded-xl shadow-sm">
                                <div>
                                    <p class="text-xs font-black text-slate-800">{{ $admin->name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400">{{ $admin->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $roles = [
                                    'super_admin' => ['المدير العام', 'gold'],
                                    'scholarship_admin' => ['مدير المنح', 'emerald'],
                                    'support_admin' => ['الدعم الفني', 'orange']
                                ];
                                $roleInfo = $roles[$admin->role] ?? ['موظف مخصص', 'slate'];
                            @endphp
                            <span class="text-[10px] font-black px-2.5 py-1 rounded-lg bg-{{ $roleInfo[1] }}-50 text-{{ $roleInfo[1] }}-600 border border-{{ $roleInfo[1] }}-100">
                                {{ $roleInfo[0] }}
                            </span>
                        </td>
                        {{-- عرض كروت الصفحات التي يمتلك الصلاحية لدخولها --}}
                        <td class="px-6 py-5">
                            <div class="flex flex-wrap gap-1 max-w-xs">
                                @if($admin->role === 'super_admin')
                                    <span class="text-[9px] font-black bg-slate-900 text-white px-2 py-0.5 rounded-md">كل صلاحيات النظام 👑</span>
                                @else
                                    {{-- فحص مصفوفة الصلاحيات --}}
                                    @if($admin->permissions && is_array($admin->permissions))
                                        @php
                                            // المصفوفة المحدثة التي تحول الكلمات المفتاحية إلى مسميات عربية مفهومة
                                            $labels = [
                                                'dashboard' => 'الرئيسية',
                                                'scholarships' => 'المنح',
                                                'students' => 'الطلاب',
                                                'applications' => 'الطلبات',
                                                'support' => 'الدعم',
                                                'contacts' => 'اتصل بنا',
                                                'admins' => 'الصلاحيات'
                                            ];
                                        @endphp

                                        @foreach($admin->permissions as $perm)
                                            <span class="text-[9px] font-bold bg-slate-50 border border-slate-100 text-slate-600 px-2 py-0.5 rounded-md">
                                                {{ $labels[$perm] ?? $perm }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-[9px] text-slate-400">لا توجد صفحات محددة</span>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-center">
                                <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $admin->status === 'active' ? 'inactive' : 'active' }}">
                                    <input type="hidden" name="name" value="{{ $admin->name }}">
                                    <input type="hidden" name="email" value="{{ $admin->email }}">
                                    <input type="hidden" name="role" value="{{ $admin->role }}">
                                    <button type="submit" class="relative inline-flex items-center cursor-pointer">
                                        <div class="w-7 h-4 {{ $admin->status === 'active' ? 'bg-emerald-500' : 'bg-slate-200' }} rounded-full transition-all after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-3 after:w-3 after:transition-all {{ $admin->status === 'active' ? 'after:translate-x-3' : '' }}"></div>
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-left">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                {{-- زر التعديل المطور --}}
                                <button @click="currentAdmin = {{ json_encode($admin) }}; editModal = true;" 
                                        class="p-2 text-slate-400 hover:text-gold-600 hover:bg-gold-100 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5"/></svg>
                                </button>
                                <form action="{{ route('admin.admins.destroy', $admin->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المدير؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-6 border-t border-slate-50">
            {{ $admins->links() }}
        </div>
    </div>

    {{-- Modal: إضافة مدير جديد --}}
    <div x-show="addModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div @click.away="addModal = false" class="bg-white rounded-[2rem] p-8 w-full max-w-lg shadow-2xl overflow-y-auto max-h-[90vh]">
            <h2 class="text-xl font-black text-slate-900 mb-6">إضافة مدير جديد وصلاحياته</h2>
            <form action="{{ route('admin.admins.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="form_name" value="add">
                <input type="text" name="name" value="{{ old('form_name') === 'add' ? old('name') : '' }}" placeholder="الاسم الكامل" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold outline-none focus:border-gold-300">
                <input type="email" name="email" value="{{ old('form_name') === 'add' ? old('email') : '' }}" placeholder="البريد الإلكتروني" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold outline-none focus:border-gold-300">
                <div class="relative" x-data="{ pwShow: false }">
                    <input type="password" :type="pwShow ? 'text' : 'password'" name="password" placeholder="كلمة المرور" required class="w-full bg-slate-50 border border-slate-100 rounded-xl py-3 pl-10 pr-4 text-sm font-bold outline-none focus:border-gold-300">
                    <button type="button" @click="pwShow = !pwShow" tabindex="-1" class="absolute inset-y-0 left-3 flex items-center text-slate-300 hover:text-gold-600 transition-colors">
                        <svg x-show="!pwShow" x-transition:enter="transition duration-500 [transition-timing-function:cubic-bezier(.68,-0.55,.27,1.55)]" x-transition:enter-start="opacity-0 scale-0 rotate-180" x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg x-show="pwShow" x-cloak x-transition:enter="transition duration-500 [transition-timing-function:cubic-bezier(.68,-0.55,.27,1.55)]" x-transition:enter-start="opacity-0 scale-0 -rotate-180" x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 012.132-3.411m3.132-2.507A9.958 9.958 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.964 9.964 0 01-1.563 3.029M3 3l18 18"></path>
                        </svg>
                    </button>
                </div>

                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase mr-2 block mb-1">الدور الوظيفي الأساسي</label>
                    <select name="role" x-model="addRole" @change="updateAddPermissions()" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold outline-none">
                        <option value="super_admin">مدير عام (كامل الصلاحيات)</option>
                        <option value="scholarship_admin">مدير منح (صلاحيات افتراضية للمنح والطلاب)</option>
                        <option value="support_admin">دعم فني (صلاحية الدعم فقط)</option>
                        <option value="custom">مخصص (تحديد يدوي مخصص)</option>
                    </select>
                </div>

                {{-- لوحة التحكم بالصلاحيات التفاعلية --}}
                <div class="border border-slate-100 rounded-2xl p-4 bg-slate-50/50 space-y-3">
                    <span class="text-[11px] font-black text-slate-500 block">تخصيص لوحات الوصول المسموحة:</span>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <template x-for="page in systemPages" :key="page.key">
                            <label class="flex items-center gap-3 bg-white p-3 rounded-xl border border-slate-100 shadow-sm cursor-pointer hover:border-navy-100 transition-all select-none">
                                <input type="checkbox" name="permissions[]" :value="page.key" x-model="addPermissions" :disabled="addRole === 'super_admin'" class="rounded border-slate-200 text-gold-600 focus:ring-gold-500 disabled:opacity-50">
                                <span class="text-lg" x-text="page.icon"></span>
                                <span class="text-xs font-black text-slate-700" x-text="page.name"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <input type="hidden" name="status" value="active">
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="addModal = false" class="flex-1 bg-slate-100 text-slate-600 py-3 rounded-xl font-black text-xs transition">إلغاء</button>
                    <button type="submit" class="flex-[2] bg-gold-600 text-white py-3 rounded-xl font-black text-xs shadow-lg hover:bg-gold-700 transition">حفظ المدير والصلاحيات</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: تعديل مدير --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div @click.away="editModal = false" class="bg-white rounded-[2rem] p-8 w-full max-w-lg shadow-2xl overflow-y-auto max-h-[90vh]">
            <h2 class="text-xl font-black text-slate-900 mb-6">تعديل صلاحيات وبيانات المدير</h2>
            
            <form :action="'{{ url('admin/admins') }}/' + currentAdmin.id" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="form_name" value="edit">
                <input type="hidden" name="id" :value="currentAdmin.id">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase mr-2">الاسم</label>
                    <input type="text" name="name" x-model="currentAdmin.name" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold outline-none focus:border-gold-300">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase mr-2">البريد الإلكتروني</label>
                    <input type="email" name="email" x-model="currentAdmin.email" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold outline-none focus:border-gold-300">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase mr-2">كلمة المرور الجديدة (اتركها فارغة لعدم التغيير)</label>
                    <div class="relative" x-data="{ pwShow: false }">
                        <input type="password" :type="pwShow ? 'text' : 'password'" name="password" placeholder="••••••••" minlength="6" class="w-full bg-slate-50 border border-slate-100 rounded-xl py-3 pl-10 pr-4 text-sm font-bold outline-none focus:border-gold-300">
                        <button type="button" @click="pwShow = !pwShow" tabindex="-1" class="absolute inset-y-0 left-3 flex items-center text-slate-300 hover:text-gold-600 transition-colors">
                            <svg x-show="!pwShow" x-transition:enter="transition duration-500 [transition-timing-function:cubic-bezier(.68,-0.55,.27,1.55)]" x-transition:enter-start="opacity-0 scale-0 rotate-180" x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="pwShow" x-cloak x-transition:enter="transition duration-500 [transition-timing-function:cubic-bezier(.68,-0.55,.27,1.55)]" x-transition:enter-start="opacity-0 scale-0 -rotate-180" x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 012.132-3.411m3.132-2.507A9.958 9.958 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.964 9.964 0 01-1.563 3.029M3 3l18 18"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase mr-2">الدور الوظيفي</label>
                    <select name="role" x-model="currentAdmin.role" @change="currentAdmin.permissions = roleDefaults[currentAdmin.role] ? [...roleDefaults[currentAdmin.role]] : currentAdmin.permissions" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold outline-none">
                        <option value="super_admin">مدير عام</option>
                        <option value="scholarship_admin">مدير منح</option>
                        <option value="support_admin">دعم فني</option>
                        <option value="custom">مخصص</option>
                    </select>
                </div>

                {{-- لوحة التحكم بالصلاحيات التفاعلية للتعديل --}}
                <div class="border border-slate-100 rounded-2xl p-4 bg-slate-50/50 space-y-3">
                    <span class="text-[11px] font-black text-slate-500 block">تعديل الصفحات المسموح فتحها للآدمن:</span>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <template x-for="page in systemPages" :key="page.key">
                            <label class="flex items-center gap-3 bg-white p-3 rounded-xl border border-slate-100 shadow-sm cursor-pointer hover:border-navy-100 transition-all select-none">
                                {{-- نقوم بفحص مصفوفة الـ permissions الخاصة بالمسؤول الحالي المختار عبر الـ Alpine --}}
                                <input type="checkbox" name="permissions[]" :value="page.key" 
                                       :checked="currentAdmin.permissions && currentAdmin.permissions.includes(page.key)"
                                       @change="
                                            if(!currentAdmin.permissions) currentAdmin.permissions = [];
                                            if($el.checked) {
                                                if(!currentAdmin.permissions.includes(page.key)) currentAdmin.permissions.push(page.key);
                                            } else {
                                                currentAdmin.permissions = currentAdmin.permissions.filter(p => p !== page.key);
                                            }
                                       "
                                       :disabled="currentAdmin.role === 'super_admin'" 
                                       class="rounded border-slate-200 text-gold-600 focus:ring-gold-500 disabled:opacity-50">
                                <span class="text-lg" x-text="page.icon"></span>
                                <span class="text-xs font-black text-slate-700" x-text="page.name"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <div class="pt-4 flex gap-2">
                    <button type="button" @click="editModal = false" class="flex-1 bg-slate-100 text-slate-600 py-3 rounded-xl font-black text-xs transition">إلغاء</button>
                    <button type="submit" class="flex-[2] bg-slate-900 text-white py-3 rounded-xl font-black text-xs shadow-lg hover:bg-gold-600 transition">تحديث البيانات والصلاحيات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection