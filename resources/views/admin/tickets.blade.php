@extends('layouts.admin')

@section('title', 'طلبات الدعم')
@section('breadcrumb', 'الدعم الفني')

@section('content')
<div class="max-w-full mx-auto space-y-6" x-data="ticketApp()" x-init="init()">
    {{-- الإحصائيات --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 w-1 h-full bg-amber-400"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">قيد الانتظار</p>
            <span class="text-2xl font-black text-slate-800">{{ $stats['pending'] }}</span>
        </div>
        <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 w-1 h-full bg-emerald-400"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">تم حلها</p>
            <span class="text-2xl font-black text-slate-800">{{ $stats['resolved'] }}</span>
        </div>
        <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 w-1 h-full bg-slate-300"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">تذاكر مغلقة</p>
            <span class="text-2xl font-black text-slate-800">{{ $stats['closed'] }}</span>
        </div>
        <div class="bg-rose-50 p-5 rounded-[1.5rem] border border-rose-100 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 w-1 h-full bg-rose-500"></div>
            <p class="text-[10px] font-black text-rose-400 uppercase mb-1">حالات عاجلة</p>
            <span class="text-2xl font-black text-rose-600">{{ sprintf('%02d', $stats['emergency']) }}</span>
        </div>
    </div>

    {{-- جدول التذاكر --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">رقم الطلب</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">الطالب</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">الموضوع</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">الحالة</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase text-left">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-slate-50/80 transition-all">
                        <td class="px-6 py-5">
                            <span class="text-xs font-black text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg">#{{ $ticket->id }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs font-black text-slate-800">{{ $ticket->user->name ?? 'مستخدم' }}</p>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs font-bold text-slate-600 truncate max-w-[200px]">{{ $ticket->subject }}</p>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-[10px] font-black px-2 py-1 rounded-lg uppercase" :class="statusClass('{{ $ticket->status }}')">{{ $ticket->status }}</span>
                        </td>
                        <td class="px-6 py-5 text-left">
                            <button @click="openChat({{ $ticket->id }})" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-[10px] font-black hover:bg-indigo-700 shadow-sm transition-all">فتح المحادثة</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400 font-bold">لا توجد طلبات حالياً</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Chat Modal --}}
    <div x-show="chatModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4">
        <div class="bg-white rounded-[2.5rem] w-full max-w-2xl max-h-[90vh] overflow-hidden shadow-2xl flex flex-col" @click.away="chatModal = false">
            {{-- Modal Header --}}
            <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/50 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg font-black">#<span x-text="activeTicket.id"></span></div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900" x-text="activeTicket.subject"></h3>
                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded" :class="statusClass(activeTicket.status)" x-text="activeTicket.status"></span>
                    </div>
                </div>
                <button @click="chatModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            {{-- Messages --}}
            <div id="admin-chat-messages" class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50/30">
                <template x-for="msg in messages" :key="msg.id">
                    <div class="flex gap-3 items-start" :class="msg.sender_type === 'admin' ? 'flex-row-reverse' : ''">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-[10px] font-black shrink-0"
                             :class="msg.sender_type === 'admin' ? 'bg-emerald-600' : (msg.sender_type === 'ai' ? 'bg-indigo-500' : 'bg-slate-700')"
                             x-text="msg.sender_type === 'admin' ? 'A' : (msg.sender_type === 'ai' ? 'AI' : 'ط')"></div>
                        <div class="p-3 rounded-2xl text-xs font-bold leading-relaxed max-w-[80%]"
                             :class="msg.sender_type === 'admin' ? 'bg-emerald-600 text-white rounded-tr-sm' : 'bg-white text-slate-700 rounded-tl-sm shadow-sm border border-slate-100'">
                            <p x-text="msg.message_text" class="whitespace-pre-line"></p>
                            <p class="text-[8px] opacity-60 mt-1" x-text="msg.created_at"></p>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Input --}}
            <div class="p-6 bg-white border-t border-slate-50 shrink-0">
                <form @submit.prevent="sendReply()" class="flex gap-3">
                    <input type="text" x-model="replyText" placeholder="اكتب ردك..." class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold outline-none focus:border-indigo-500 transition-all" :disabled="loading">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-xs font-black hover:bg-indigo-700 disabled:opacity-50" :disabled="loading || !replyText.trim()">إرسال</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function ticketApp() {
    return {
        chatModal: false,
        activeTicket: {},
        messages: [],
        replyText: '',
        loading: false,

init() {
    // ✅ Real-time باستخدام Echo - يستقبل الرسائل فور إرسالها
    if (window.Echo) {
        Echo.private('admin.support')
            .listen('ChatMessageSent', (e) => {
                console.log('📩 Admin Received Message:', e.message);
                
                // إذا كان الأدمن فاتح المودال لنفس التذكرة - أضف الرسالة فوراً
                if (this.chatModal && this.activeTicket.id == e.message.messageable_id) {
                    this.messages.push({
                        id: e.message.id,
                        sender_type: e.message.sender_type,
                        message_text: e.message.message_text,
                        file_path: e.message.file_path,
                        created_at: e.message.created_at
                    });
                    this.scrollToBottom();
                }
            });
        
        // ✅ استمع لإنشاء تذكرة جديدة
        Echo.private('admin.support')
            .listen('NewTicketCreated', (e) => {
                console.log('📥 New Ticket Created:', e.ticket);
                // أضف التذكرة الجديدة للقائمة
                this.loadTickets();
            });
    }

    // ✅ Polling للتحديث التلقائي كل 5 ثوانٍ (احتياطي)
    this.pollInterval = setInterval(() => {
        this.loadTickets();
    }, 5000);
},

loadTickets() {
    // تحديث قائمة التذاكر عبر JSON API (باستخدام query parameter)
    fetch('/admin/tickets?api=true', { 
        headers: { 
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.tickets && data.stats) {
            // تحديث الإحصائيات
            const statBoxes = document.querySelectorAll('.grid.grid-cols-2 span.text-2xl');
            if (statBoxes[0]) statBoxes[0].textContent = data.stats.pending;
            if (statBoxes[1]) statBoxes[1].textContent = data.stats.resolved;
            if (statBoxes[2]) statBoxes[2].textContent = data.stats.closed;
            if (statBoxes[3]) statBoxes[3].textContent = String(data.stats.emergency).padStart(2, '0');
        }
    })
    .catch(err => console.error("Poll error:", err));
},

        statusClass(status) {
            const classes = { 'pending': 'bg-amber-50 text-amber-600', 'resolved': 'bg-emerald-50 text-emerald-600', 'closed': 'bg-slate-100 text-slate-500' };
            return classes[status] || 'bg-slate-50 text-slate-400';
        },

        openChat(ticketId) {
            this.messages = [];
            fetch(`/admin/tickets/${ticketId}`, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    this.activeTicket = data;
                    this.messages = data.messages || [];
                    this.chatModal = true;
                    this.scrollToBottom();
                }).catch(err => console.error("Error opening chat:", err));
        },

        async sendReply() {
            if (!this.replyText.trim() || this.loading) return;
            this.loading = true;

            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const res = await fetch(`/admin/tickets/${this.activeTicket.id}/reply`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ message: this.replyText })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.messages.push(data.message);
                    this.replyText = '';
                    this.scrollToBottom();
                } else {
                    alert(data.error || 'فشل إرسال الرد');
                }
            } catch (e) {
                console.error("Reply Error:", e);
            } finally {
                this.loading = false;
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('admin-chat-messages');
                if (container) container.scrollTop = container.scrollHeight;
            });
        }
    };
}
</script>
@endsection