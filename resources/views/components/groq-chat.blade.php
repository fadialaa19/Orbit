<div id="groq-chat-widget"
     class="fixed bottom-6 right-6 z-[9999] rtl:font-sans"
     style="font-family:'Cairo',sans-serif;direction:rtl;"
     x-data="{
        open: false,
        input: '',
        loading: false,
        handoff: false,
        banned: false,
        isAuthenticated: false,
        supportTicketId: null,
        supportCreated: false,
        messages: [],
        msgId: 0,

        init() {
            // Check if user was banned in previous session (localStorage)
            this.banned = localStorage.getItem('groq_chat_banned') === 'true';
            this.supportTicketId = localStorage.getItem('groq_chat_support_ticket_id');
            if (this.supportTicketId) {
                this.supportCreated = true;
            }
        },

        toggle() {
            this.open = !this.open;
            if (this.open) this.$nextTick(() => this.scrollToBottom());
        },

        async sendMessage() {
            const text = this.input.trim();
            if (!text || this.loading || this.banned) return;
            this.input = '';
            this.loading = true;
            this.handoff = false;
            this.messages.push({ id: ++this.msgId, role: 'user', content: this.escapeHtml(text) });
            this.scrollToBottom();
            try {
                const res = await fetch('{{ route('api.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: text })
                });
                const data = await res.json();
                if (!data.success) {
                    this.messages.push({ id: ++this.msgId, role: 'ai', content: '⚠️ ' + (data.reply || 'Error') });
                } else {
                    this.messages.push({ id: ++this.msgId, role: 'ai', content: data.reply });

                    // Handle permanent ban
                    if (data.force_close) {
                        this.banned = true;
                        localStorage.setItem('groq_chat_banned', 'true');
                    }

        // Handle support ticket creation
                    if (data.trigger_support) {
                        this.handoff = true;
                        this.isAuthenticated = data.is_authenticated;
                        if (data.support_ticket_id) {
                            this.supportTicketId = data.support_ticket_id;
                            this.supportCreated = true;
                            localStorage.setItem('groq_chat_support_ticket_id', data.support_ticket_id);
                        }
                    }
                }
            } catch (e) {
                this.messages.push({ id: ++this.msgId, role: 'ai', content: '⚠️ Failed to connect. Retry later.' });
            }
            this.loading = false;
            this.scrollToBottom();
        },

        async clearChat() {
            this.messages = [];
            this.handoff = false;
            this.supportTicketId = null;
            this.supportCreated = false;
            this.msgId = 0;
            localStorage.removeItem('groq_chat_support_ticket_id');
            await fetch('{{ route('api.chat.clear') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') }
            });
        },

        async clearBan() {
            this.banned = false;
            this.messages = [];
            this.msgId = 0;
            localStorage.removeItem('groq_chat_banned');
            await fetch('{{ route('api.chat.clear-ban') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') }
            });
        },

        escapeHtml(text) {
            const d = document.createElement('div');
            d.textContent = text;
            return d.innerHTML;
        },

        formatMessage(text) {
            return (text || '').replace(/\n/g, '<br>');
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const c = document.getElementById('groq-chat-messages');
                if (c) c.scrollTop = c.scrollHeight;
            });
        }
     }">

    {{-- Toggle Button --}}
    <button @click="toggle()"
            class="w-14 h-14 rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-95"
            :class="open ? 'bg-rose-500 hover:bg-rose-600' : 'bg-indigo-600 hover:bg-indigo-700'">
        <svg x-show="!open" class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <svg x-show="open" x-cloak class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    {{-- Chat Window --}}
    <div x-show="open" x-cloak x-transition
         class="absolute bottom-16 right-0 w-[340px] md:w-[380px] bg-white rounded-[2rem] shadow-2xl border border-slate-100 overflow-hidden flex flex-col"
         style="max-height:600px;height:500px;">

        {{-- Header --}}
        <div class="bg-indigo-600 p-4 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-lg">🤖</div>
                <div>
                    <h4 class="font-black text-sm">مستشار منحي الذكي</h4>
                    <p class="text-[10px] opacity-80 font-bold">مدعوم بـ Groq AI</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span x-show="!banned" class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                <span x-show="banned" class="w-2 h-2 bg-rose-500 rounded-full"></span>
                <span x-show="!banned" class="text-[10px] font-bold">متصل</span>
                <span x-show="banned" class="text-[10px] font-bold">مغلق</span>
            </div>
        </div>

        {{-- Messages Area --}}
        <div id="groq-chat-messages"
             class="flex-1 overflow-y-auto p-4 bg-slate-50 space-y-4 relative"
             style="scrollbar-width:thin;">

            {{-- Welcome --}}
            <div x-show="!banned" class="flex gap-3 items-start">
                <div class="w-8 h-8 bg-indigo-600 rounded-xl flex items-center justify-center text-white text-sm shrink-0">AI</div>
                <div class="bg-white p-3 rounded-2xl rounded-tr-sm shadow-sm border border-slate-100 text-sm font-bold text-slate-700 leading-relaxed max-w-[85%]">
                    مرحباً! أنا مستشارك الذكي في شؤون المنح الدراسية. كيف يمكنني مساعدتك اليوم؟ 💡
                </div>
            </div>

            {{-- Banned Overlay --}}
            <div x-show="banned" x-cloak x-transition
                 class="absolute inset-0 bg-rose-50/95 backdrop-blur-sm z-10 flex flex-col items-center justify-center p-6 text-center">
                <div class="text-4xl mb-3">🚫</div>
                <h3 class="text-base font-black text-rose-800 mb-2">تم إغلاق المحادثة نهائياً</h3>
                <p class="text-xs font-bold text-rose-600 mb-4 leading-relaxed">
                    تم إغلاق هذه المحادثة بسبب استخدامك لألفاظ غير لائقة.<br>
                    إذا كنت تعتقد أن هذا خطأ، يمكنك التواصل مع الدعم الفني.
                </p>
                <a href="{{ url('/#contact') }}"
                   class="inline-block bg-rose-600 text-white px-5 py-2.5 rounded-xl text-xs font-black hover:bg-rose-700 transition shadow-lg shadow-rose-200 mb-2">
                    📞 تواصل مع الدعم الفني
                </a>
                <button @click="clearBan()"
                        class="text-[10px] font-bold text-rose-400 hover:text-rose-600 underline mt-2">
                    المحاولة مرة أخرى
                </button>
            </div>

            <template x-for="msg in messages" :key="msg.id">
                <div class="flex gap-3 items-start animate-in fade-in slide-in-from-bottom-2 duration-300"
                     :class="msg.role === 'user' ? 'flex-row-reverse' : ''">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-sm shrink-0"
                         :class="msg.role === 'user' ? 'bg-slate-700' : 'bg-indigo-600'"
                         x-text="msg.role === 'user' ? 'أنت' : 'AI'"></div>
                    <div class="p-3 rounded-2xl text-sm font-bold leading-relaxed max-w-[85%]"
                         :class="msg.role === 'user'
                             ? 'bg-indigo-600 text-white rounded-tl-sm'
                             : 'bg-white text-slate-700 rounded-tr-sm shadow-sm border border-slate-100'">
                        <span x-html="formatMessage(msg.content)"></span>
                    </div>
                </div>
            </template>

            {{-- Loading --}}
            <div x-show="loading && !banned" class="flex gap-3 items-start">
                <div class="w-8 h-8 bg-indigo-600 rounded-xl flex items-center justify-center text-white text-sm shrink-0">AI</div>
                <div class="bg-white p-4 rounded-2xl rounded-tr-sm shadow-sm border border-slate-100">
                    <div class="flex gap-1 items-center h-3">
                        <span class="w-2 h-2 bg-slate-300 rounded-full animate-bounce"></span>
                        <span class="w-2 h-2 bg-slate-300 rounded-full animate-bounce [animation-delay:-0.2s]"></span>
                        <span class="w-2 h-2 bg-slate-300 rounded-full animate-bounce [animation-delay:-0.4s]"></span>
                    </div>
                </div>
            </div>

            {{-- Support Ticket Created --}}
            <div x-show="supportCreated && handoff" x-cloak x-transition
                 class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl text-center">
                <div class="text-2xl mb-2">✅</div>
                <p class="text-sm font-black text-emerald-800 mb-1">تم إنشاء طلب دعم فني</p>
                <p class="text-xs font-bold text-emerald-600 mb-2">
                    رقم الطلب: <span class="font-black text-emerald-900" x-text="'#' + supportTicketId"></span>
                </p>
                <p class="text-[10px] text-emerald-500 font-bold mb-3">
                    سيتواصل معك فريق الدعم الفني في أقرب وقت ممكن.
                </p>
                <div class="flex gap-2 justify-center">
                    <a href="{{ url('/#contact') }}"
                       class="inline-block bg-emerald-500 text-white px-4 py-2 rounded-xl text-[10px] font-black hover:bg-emerald-600 transition">
                        📞 تواصل معنا
                    </a>
                    <button @click="clearChat()"
                            class="inline-block bg-white border border-emerald-200 text-emerald-600 px-4 py-2 rounded-xl text-[10px] font-black hover:bg-emerald-50 transition">
                        محادثة جديدة
                    </button>
                </div>
            </div>

            {{-- Guest Support Handoff --}}
            <div x-show="handoff && !supportCreated && !isAuthenticated" x-cloak x-transition
                 class="bg-amber-50 border border-amber-200 p-4 rounded-2xl text-center">
                <div class="text-2xl mb-2">🤝</div>
                <p class="text-sm font-black text-amber-800 mb-1">يبدو أنك تحتاج مساعدة متخصصة</p>
                <p class="text-xs font-bold text-amber-600 mb-3">
                    يرجى تسجيل الدخول لإنشاء طلب دعم فني، أو استخدم صفحة التواصل.
                </p>
                <div class="flex gap-2 justify-center">
                    <a href="{{ route('login') }}"
                       class="inline-block bg-amber-500 text-white px-4 py-2 rounded-xl text-[10px] font-black hover:bg-amber-600 transition">
                        🔐 تسجيل الدخول
                    </a>
                    <a href="{{ url('/#contact') }}"
                       class="inline-block bg-white border border-amber-200 text-amber-600 px-4 py-2 rounded-xl text-[10px] font-black hover:bg-amber-50 transition">
                        📞 تواصل معنا
                    </a>
                </div>
            </div>

            <div id="groq-chat-anchor"></div>
        </div>

        {{-- Input --}}
        <div x-show="!banned && !supportCreated" class="p-4 bg-white border-t border-slate-100 shrink-0">
            <form @submit.prevent="sendMessage()" class="flex gap-2">
                <input type="text" x-model="input" placeholder="اكتب سؤالك هنا..."
                       class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold outline-none focus:border-indigo-500 focus:bg-white transition-all"
                       :disabled="loading"
                       @keydown.enter.prevent="sendMessage()">
                <button type="submit"
                        class="bg-indigo-600 text-white w-10 h-10 rounded-xl flex items-center justify-center hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="loading || !input.trim()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </button>
                <button type="button" @click="clearChat()"
                        class="bg-slate-100 text-slate-400 w-10 h-10 rounded-xl flex items-center justify-center hover:bg-rose-100 hover:text-rose-500 transition"
                        title="مسح المحادثة">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
            <p class="text-[9px] text-slate-300 text-center mt-2 font-bold">مدعوم بـ Groq AI · قد يحتوي على أخطاء</p>
        </div>
    </div>
</div>

