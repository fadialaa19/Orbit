@extends('layouts.dashboard')

@section('title', 'محادثة الدعم')

@section('content')
<div class="max-w-4xl mx-auto space-y-4" x-data="studentChat()" x-init="init()" x-cloak>
    
    {{-- Header --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5 flex items-center justify-between sticky top-0 z-10 backdrop-blur-md bg-white/90">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black border border-indigo-100">
                #{{ $ticket->id }}
            </div>
            <div>
                <h1 class="text-sm font-black text-slate-900">{{ $ticket->subject }}</h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="isClosed ? 'bg-slate-400' : 'bg-emerald-400'"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2" :class="isClosed ? 'bg-slate-500' : 'bg-emerald-500'"></span>
                    </span>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider" x-text="isClosed ? 'تذكرة مغلقة' : 'محادثة نشطة'"></p>
                </div>
            </div>
        </div>
        <a href="{{ route('dashboard.tickets') }}" class="px-4 py-2 bg-slate-50 hover:bg-slate-100 rounded-xl text-xs font-bold text-slate-500 transition-all flex items-center gap-2">
            <span>&#8594;</span> رجوع
        </a>
    </div>

    {{-- Chat Messages --}}
    <div id="student-chat-messages" 
         class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 h-[550px] overflow-y-auto space-y-6 scroll-smooth">
        
        <template x-for="msg in messages" :key="msg.id">
            <div class="flex gap-3" :class="msg.sender_type === 'user' ? 'flex-row-reverse' : 'flex-row'">
                <div class="w-9 h-9 rounded-2xl flex items-center justify-center text-white text-[10px] font-black shrink-0 shadow-sm"
                     :class="msg.sender_type === 'admin' ? 'bg-emerald-500' : (msg.sender_type === 'ai' ? 'bg-purple-500' : 'bg-slate-800')"
                     x-text="msg.sender_type === 'admin' ? 'AD' : (msg.sender_type === 'ai' ? 'AI' : 'ME')">
                </div>

                <div class="max-w-[75%] space-y-1">
                    <div class="p-4 rounded-3xl text-xs font-bold leading-relaxed shadow-sm border"
                         :class="msg.sender_type === 'user' 
                            ? 'bg-indigo-600 text-white rounded-tr-none border-indigo-500' 
                            : (msg.sender_type === 'admin' 
                                ? 'bg-white text-slate-700 rounded-tl-none border-slate-100' 
                                : 'bg-purple-50 text-purple-900 rounded-tl-none border-purple-100')">
                        
                        <p x-text="msg.message_text" class="white-space-pre-wrap break-words"></p>
                        
                        <template x-if="msg.file_path">
                            <div class="mt-3 pt-3 border-t border-white/20">
                                <a :href="'/storage/' + msg.file_path" target="_blank" 
                                   class="flex items-center gap-2 text-[10px] hover:underline"
                                   :class="msg.sender_type === 'user' ? 'text-indigo-100' : 'text-indigo-600'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    مرفق خارجي
                                </a>
                            </div>
                        </template>
                    </div>
                    <p class="text-[9px] text-slate-400 font-medium px-2" :class="msg.sender_type === 'user' ? 'text-left' : 'text-right'" x-text="msg.created_at"></p>
                </div>
            </div>
        </template>

        <div x-show="loading" class="flex gap-3 flex-row-reverse animate-pulse">
            <div class="w-9 h-9 bg-slate-200 rounded-2xl"></div>
            <div class="bg-slate-100 p-4 rounded-3xl rounded-tr-none w-32 h-12"></div>
        </div>

        <div x-show="messages.length === 0 && !loading" class="flex flex-col items-center justify-center py-20 text-slate-400">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-2xl">💬</div>
            <p class="text-sm font-bold">ابدأ المحادثة الآن، نحن هنا للمساعدة</p>
        </div>
    </div>

    {{-- Reply Input Area --}}
    <div class="bg-white rounded-[2rem] shadow-lg border border-slate-100 p-4" x-show="!isClosed">
        <form @submit.prevent="sendReply()" class="flex flex-col gap-3">
            <div class="flex items-end gap-3">
                <input type="file" x-ref="fileInput" class="hidden" accept="image/*,.pdf" @change="replyFile = $event.target.files[0]">
                
                <button type="button" @click="$refs.fileInput.click()" 
                        class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 transition-all shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </button>

                <textarea x-model="replyText" 
                          rows="1"
                          @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                          placeholder="اكتب رسالتك هنا..."
                          class="flex-1 bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 text-sm font-bold outline-none focus:border-indigo-500 focus:bg-white transition-all resize-none max-h-32"
                          @keydown.enter.prevent="if(!loading && replyText.trim()) sendReply()"></textarea>

                <button type="submit" 
                        class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200 transition-all disabled:opacity-50 disabled:grayscale shrink-0"
                        :disabled="loading || !replyText.trim()">
                    <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>

            <div x-show="replyFile" class="flex items-center gap-2 bg-indigo-50 border border-indigo-100 p-2 rounded-xl w-fit">
                <span class="text-[10px] font-black text-indigo-600 px-2" x-text="replyFile ? replyFile.name : ''"></span>
                <button type="button" @click="replyFile = null" class="text-indigo-400 hover:text-rose-500 font-bold p-1">&times;</button>
            </div>
        </form>
    </div>

    {{-- Closed Ticket Message & AI --}}
    <div x-show="isClosed" class="space-y-4">
        <div class="bg-rose-50 border border-rose-100 rounded-3xl p-6 text-center">
            <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-3 text-xl">🔒</div>
            <h3 class="text-sm font-black text-rose-900 mb-1">هذه التذكرة مغلقة</h3>
            <p class="text-xs text-rose-600 font-bold">تم حل المشكلة أو إغلاق الطلب، يمكنك الاستعانة بالذكاء الاصطناعي للمساعدة الفورية.</p>
        </div>
        
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-2">
             <x-ticket-ai-chat :ticket-id="$ticket->id" />
        </div>
    </div>
</div>

<script>
function studentChat() {
    return {
        messages: [],
        ticket: null,
        replyText: '',
        replyFile: null,
        loading: false,
        ticketId: {{ $ticket->id }},
        pollInterval: null,
        isClosed: {{ $ticket->status === 'closed' ? 'true' : 'false' }},

init() {
            this.loadMessages().then(() => this.scrollToBottom());
            
            // ✅ Real-time باستخدام Echo - يستقبل الرسائل فور وصولها
            // Guard: Echo.private() must exist before we call it (prevents Alpine crashes).
            if (window.Echo && typeof window.Echo.private === 'function') {
                const userId = {{ auth()->id() }};
                window.Echo.private('App.Models.User.' + userId)
                    .listen('ChatMessageSent', (e) => {
                        console.log('📩 New Message Received:', e.message);
                        this.messages.push({
                            id: e.message.id,
                            sender_type: e.message.sender_type,
                            message_text: e.message.message_text,
                            file_path: e.message.file_path,
                            created_at: e.message.created_at
                        });
                        this.scrollToBottom();
                    });
            } else {
                console.warn('Echo.private() not ready yet');
            }

            // ✅ Polling موحّد كل ثانيتين
            // ملاحظة: لو Echo شغال فسيكون Polling كـ fallback فقط.
            // ✅ جلب الرسائل كل 2 ثانية فقط إذا زادت الرسائل (منع التحديث المزعج)
            // سنراقب عدد الرسائل الحالية، وعند الزيادة فقط نعمل scroll.
                this.pollInterval = setInterval(async () => {
                if (this.loading || this.isClosed) return;

                const oldLength = this.messages.length;
                const res = await this.loadMessages();
                // if loadMessages added something, it will have updated this.messages
                if (this.messages.length > oldLength) {
                    this.scrollToBottom();
                }
            }, 2000);
        },

        async loadMessages() {
            try {
                const res = await fetch(`/tickets/${this.ticketId}/messages`);
                const text = await res.text();
                const data = text ? JSON.parse(text) : {};

                if (!res.ok) {
                    console.error('ticketMessages HTTP error', res.status, data);
                    return { success: false };
                }

                if (data.success) {
                    const oldLength = this.messages.length;
                    this.messages = data.messages;
                    this.isClosed = data.ticket.status === 'closed';
                    if (this.messages.length > oldLength) {
                        this.scrollToBottom();
                    }
                } else {
                    console.error('ticketMessages returned success=false', data);
                }
            } catch (e) {
                console.error('Error fetching messages', e);
            }
        },

        async sendReply() {
            if (this.isClosed || !this.replyText.trim()) return;

            this.loading = true;
            const formData = new FormData();
            formData.append('message', this.replyText);
            if (this.replyFile) formData.append('file', this.replyFile);

            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const res = await fetch(`/tickets/${this.ticketId}/reply`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    this.replyText = '';
                    this.replyFile = null;
                    await this.loadMessages();
                    this.scrollToBottom();
                } else {
                    alert(data.message);
                }
            } catch (e) { alert('خطأ في الإرسال'); }
            this.loading = false;
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = document.getElementById('student-chat-messages');
                if (el) el.scrollTop = el.scrollHeight;
            });
        }
    };
}
</script>
@endsection