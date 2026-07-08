@extends('layouts.dashboard')
@section('title', 'التنبيهات')

@section('content')
<div class="bg-slate-50 min-h-screen py-10 px-4 md:px-10" dir="rtl" x-data="notificationsHub()" x-init="init()" x-cloak>
    <div class="max-w-7xl mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div>
                <h1 class="text-3xl font-black text-slate-800 flex items-center gap-3">
                    التنبيهات <span class="text-2xl text-indigo-600">(<span x-text="stats.count">0</span> جديدة)</span>
                </h1>
                <p class="text-slate-500 font-bold mt-2">ابقَ على اطلاع بالتحديثات والفرص</p>
            </div>

            <div class="flex gap-3">
                <button class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-black shadow-lg hover:bg-indigo-700" type="button" @click="markAllAsRead">
                    قراءة الكل
                </button>
                <button class="border border-slate-200 text-slate-500 px-6 py-3 rounded-2xl font-bold hover:bg-slate-50" type="button" @click="filter='unread'">
                    تنقيح
                </button>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-50 p-2 mb-8">
            <div class="flex bg-slate-50/50 rounded-2xl overflow-hidden">
                <button @click="tab = 'new'"
                    :class="tab === 'new' ? 'bg-white shadow-sm text-indigo-600 border-b-2 border-indigo-500' : 'text-slate-400 hover:text-slate-600'"
                    class="flex-1 py-4 font-black text-sm transition" type="button">
                    الجديدة
                </button>
                <button @click="tab = 'old'"
                    :class="tab === 'old' ? 'bg-white shadow-sm text-indigo-600 border-b-2 border-indigo-500' : 'text-slate-400 hover:text-slate-600'"
                    class="flex-1 py-4 font-black text-sm transition" type="button">
                    السابقة
                </button>
            </div>
        </div>

        <div class="space-y-4 mb-10">
            <!-- Loading -->
            <div class="space-y-4" x-show="loading">
                <template x-for="n in 5" :key="n">
                    <div class="h-24 bg-white rounded-[2.5rem] border border-slate-100 animate-pulse"></div>
                </template>
            </div>

            <!-- Content -->
            <div class="space-y-4" x-show="!loading">
                <template x-if="visibleNotifications.length === 0">
                    <div class="text-center py-16">
                        <div class="w-24 h-24 mx-auto mb-6 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 text-3xl">🔔</div>
                        <h3 class="text-xl font-black text-slate-500 mb-2">لا توجد تنبيهات</h3>
                        <p class="text-slate-400 font-bold">ستظهر التنبيهات الجديدة هنا</p>
                    </div>
                </template>

                <template x-for="n in visibleNotifications" :key="n.id">
                    <div class="bg-white rounded-[2.5rem] p-6 shadow-sm border border-slate-100 hover:shadow-md transition flex items-start gap-4 group"
                         :class="n.is_read ? '' : 'ring-2 ring-indigo-100 bg-indigo-50/50'">
                        <div class="w-12 h-12 bg-gradient-to-r"
                             :class="n.is_read ? 'from-slate-100 to-slate-200' : 'from-indigo-600 to-purple-600'"
                             rounded-2xl flex items-center justify-center text-white font-black text-xl flex-shrink-0 mt-1">
                            <span x-text="n.icon ?? '🔔'"></span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs text-slate-400 font-bold" x-text="n.title"></span>
                                <template x-if="!n.is_read">
                                    <span class="w-2 h-2 bg-indigo-600 rounded-full"></span>
                                </template>
                            </div>
                            <p class="font-black text-slate-800 text-sm mb-2 leading-relaxed" x-text="n.text"></p>

                            <a :href="n.url || '#'
                                    " class="text-indigo-600 text-xs font-bold hover:underline inline-flex items-center gap-1">
                                عرض <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Keep original empty section style for fallback -->
    </div>

    <script>
        function notificationsHub() {
            return {
                loading: true,
                tab: 'new',
                filter: 'all',
                notifications: [],
                stats: { count: 0 },

                init() {
                    this.refresh();
                },

                get visibleNotifications() {
                    const list = this.notifications || [];
                    if (this.tab === 'new') return list.filter(n => !n.is_read);
                    return list.filter(n => n.is_read);
                },

                async refresh() {
                    this.loading = true;
                    try {
                        const res = await fetch('/dashboard/my-notifications/api', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (!res.ok) throw new Error('Failed to load notifications');
                        const data = await res.json();
                        this.notifications = (data.notifications || []).map(n => ({
                            ...n,
                            icon: n.icon ?? (n.is_read ? '🔔' : '💥')
                        }));
                        this.stats.count = this.notifications.filter(n => !n.is_read).length;
                    } catch (e) {
                        console.error(e);
                        this.notifications = [];
                        this.stats.count = 0;
                    } finally {
                        this.loading = false;
                    }
                },

                async markAllAsRead() {
                    try {
                        await fetch('{{ route("dashboard.notifications.read-all") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                        });
                    } catch (e) {
                        console.error(e);
                    }
                    this.notifications = (this.notifications || []).map(n => ({ ...n, is_read: true, read_at: n.read_at ?? new Date().toISOString() }));
                    this.stats.count = 0;
                    this.tab = 'old';
                }
            };
        }
    </script>
</div>
@endsection

