@extends('layouts.dashboard')

@section('title', 'طلباتي / التذاكر')

@section('header_search', '')

@section('content')
<div class="bg-slate-50 min-h-screen py-10 px-4 md:px-10" dir="rtl" x-data="ticketsHub()" x-init="init()" x-cloak>
    <div class="max-w-6xl mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div>
                <h1 class="text-3xl font-black text-slate-800">طلباتي</h1>
                <p class="text-slate-500 font-bold mt-2">تتبع تذاكر الدعم والمحادثات المرتبطة بها</p>
            </div>

            <div class="flex gap-3">
                <button type="button" @click="refresh()" class="bg-gold-600 text-white px-6 py-3 rounded-2xl font-black shadow-lg hover:bg-gold-700 transition">
                    تحديث
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-50 text-center">
                <p class="text-xs font-black text-slate-400 uppercase">قيد الانتظار</p>
                <p class="text-3xl font-black text-gold-600 mt-2" x-text="stats.pending ?? 0"></p>
            </div>
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-50 text-center">
                <p class="text-xs font-black text-slate-400 uppercase">محلولة</p>
                <p class="text-3xl font-black text-gold-600 mt-2" x-text="stats.resolved ?? 0"></p>
            </div>
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-50 text-center">
                <p class="text-xs font-black text-slate-400 uppercase">مغلقة</p>
                <p class="text-3xl font-black text-slate-700 mt-2" x-text="stats.closed ?? 0"></p>
            </div>
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-50 text-center">
                <p class="text-xs font-black text-slate-400 uppercase">عاجلة</p>
                <p class="text-3xl font-black text-rose-600 mt-2" x-text="stats.emergency ?? 0"></p>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-50 overflow-hidden">
            <div class="p-5 border-b border-slate-50 bg-slate-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                <div class="text-right">
                    <h2 class="text-lg font-black text-slate-800">قائمة التذاكر</h2>
                    <p class="text-xs text-slate-400 font-bold">اضغط على التذكرة لفتح المحادثة</p>
                </div>
                <div class="text-right">
                    <input type="text" class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold outline-none focus:border-navy-100 w-full md:w-72"
                           placeholder="ابحث بالعنوان..." x-model="query" @input.debounce.300ms="filter()" />
                </div>
            </div>

            <div class="p-5">
                <div class="space-y-3" x-show="loading">
                    <div class="h-14 bg-slate-100 rounded-2xl animate-pulse"></div>
                    <div class="h-14 bg-slate-100 rounded-2xl animate-pulse"></div>
                    <div class="h-14 bg-slate-100 rounded-2xl animate-pulse"></div>
                </div>

                <template x-if="!loading">
                    <div class="space-y-3">
                        <template x-for="t in filteredTickets" :key="t.id">
                            <a :href="routeTicketShow(t.id)" class="group block rounded-3xl border border-slate-100 hover:border-gold-100 hover:bg-gold-100/20 transition p-5">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-white text-xs shadow-sm"
                                                 :class="statusClass(t.status)" x-text="statusIcon(t.status)"></div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-black text-slate-800 truncate" x-text="t.subject"></p>
                                                <p class="text-[10px] text-slate-400 font-bold mt-1" x-text="t.created_at"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] px-3 py-1 rounded-full font-black"
                                              :class="statusPillClass(t.status)" x-text="statusLabel(t.status)"></span>
                                        <span class="text-gold-600 font-black text-sm group-hover:translate-x-[-2px] transition">→</span>
                                    </div>
                                </div>
                            </a>
                        </template>

                        <div x-show="filteredTickets.length === 0" class="py-16 text-center">
                            <div class="text-6xl opacity-20">🎫</div>
                            <p class="text-slate-400 font-bold mt-3">لا توجد تذاكر مطابقة</p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>

    <script>
        function ticketsHub() {
            return {
                loading: true,
                tickets: [],
                filteredTickets: [],
                query: '',
                stats: {},

                init() {
                    this.refresh();
                },

                async refresh() {
                    this.loading = true;
                    this.query = this.query || '';

                    try {
                        // Admin controller supports polling via AJAX, but we need user tickets.
                        // We'll call a lightweight endpoint using existing student routes if exists.
                        // Fallback: if endpoint fails, show empty.
const res = await fetch('/dashboard/my-tickets/api', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        if (!res.ok) throw new Error('Failed to load tickets');
                        const data = await res.json();

this.tickets = data.tickets || [];

                        // NOTE: later we will add dedicated endpoint for student-only tickets.
                        // UI remains functional even if backend returns mixed tickets.
                        this.stats = data.stats || {};
                        this.filteredTickets = this.tickets;
                        this.filter();
                    } catch (e) {
                        console.error(e);
                        this.tickets = [];
                        this.filteredTickets = [];
                        this.stats = {};
                    } finally {
                        this.loading = false;
                    }
                },

                filter() {
                    const q = (this.query || '').trim().toLowerCase();
                    if (!q) {
                        this.filteredTickets = this.tickets;
                        return;
                    }
                    this.filteredTickets = this.tickets.filter(t => (t.subject || '').toLowerCase().includes(q));
                },

                statusIcon(status) {
                    if (status === 'pending') return '⏳';
                    if (status === 'resolved') return '✅';
                    if (status === 'closed') return '🔒';
                    return '⚠️';
                },
                statusClass(status) {
                    if (status === 'pending') return 'bg-yellow-500/90';
                    if (status === 'resolved') return 'bg-emerald-500/90';
                    if (status === 'closed') return 'bg-slate-700/90';
                    return 'bg-rose-500/90';
                },
                statusPillClass(status) {
                    if (status === 'pending') return 'bg-yellow-50 text-yellow-700 border border-yellow-100';
                    if (status === 'resolved') return 'bg-emerald-50 text-emerald-700 border border-emerald-100';
                    if (status === 'closed') return 'bg-slate-100 text-slate-700 border border-slate-200';
                    return 'bg-rose-50 text-rose-700 border border-rose-100';
                },
                statusLabel(status) {
                    if (status === 'pending') return 'قيد الانتظار';
                    if (status === 'resolved') return 'محلولة';
                    if (status === 'closed') return 'مغلقة';
                    return 'عاجلة';
                },

                // Build URL without relying on route() inside JS.
                routeTicketShow(id) {
                    return '/dashboard/my-tickets/' + id;
                }
            }
        }
    </script>
</div>
@endsection

