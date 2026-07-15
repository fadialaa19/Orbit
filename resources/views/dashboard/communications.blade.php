@extends('layouts.dashboard')

@section('title', 'مركز التواصل')

@section('content')
<script>window.userId = {{ Auth::id() }};</script>
<div class="max-w-7xl mx-auto h-[calc(100vh-69px)] flex bg-gradient-to-b from-slate-50 to-white">
    <div x-data="communicationHub()" x-init="init()" class="w-full flex overflow-hidden rounded-3xl shadow-2xl m-4 border border-slate-100/50 backdrop-blur-xl bg-white/80">
        
        <div class="w-20 flex flex-col bg-gradient-to-b from-gold-500 to-gold-600 text-white p-4 gap-4">
            <button @click="switchPanel('ai')" :class="activePanel === 'ai' ? 'bg-white/20 rounded-2xl p-2 shadow-lg' : 'hover:bg-white/10 rounded-xl p-2'" title="الذكاء الاصطناعي">
                <svg class="w-7 h-7 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </button>
            <button @click="switchPanel('support')" :class="activePanel === 'support' ? 'bg-white/20 rounded-2xl p-2 shadow-lg' : 'hover:bg-white/10 rounded-xl p-2'" title="الدعم الفني">
                <svg class="w-7 h-7 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </button>
        </div>

        <div class="flex-1 max-w-md border-l border-slate-100/50 bg-gradient-to-b from-slate-50/50 backdrop-blur-sm flex flex-col">
            <div class="p-4 border-b border-slate-100/50 bg-white/70 flex justify-between items-center">
                <h2 x-text="panelTitles[activePanel]" class="text-xl font-black text-slate-900"></h2>
                
                <button @click="createNew()" class="p-2 bg-gold-600 text-white rounded-xl hover:bg-gold-700 shadow-md transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </button>
            </div>

            <div class="overflow-y-auto flex-1">
                <template x-for="chat in chatsByType[activePanel]" :key="chat.id">
                    <div @click="selectChat(chat)"
                            :class="selectedChat?.id === chat.id ? 'bg-white shadow-lg border-r-4 border-gold-500' : 'hover:bg-white/50 border-r-4 border-transparent'"
                            class="w-full group flex items-center gap-4 p-4 transition-all cursor-pointer">
                        <div class="w-12 h-12 bg-gradient-to-r from-gold-500 to-gold-600 rounded-2xl flex items-center justify-center text-white font-black shadow-lg shrink-0">
                            <span x-text="chat.avatar || (activePanel === 'ai' ? '🤖' : '🛠️')"></span>
                        </div>
                        <div class="flex-1 text-right min-w-0">
                            <div class="flex justify-between items-start gap-2">
                                <p class="font-black text-slate-900 text-sm truncate" x-text="chat.name || chat.subject"></p>
                                <span x-show="chat.status" :class="statusColor(chat.status)" class="text-[8px] px-2 py-0.5 rounded-full font-bold uppercase shrink-0" x-text="chat.status"></span>
                            </div>
                            <p class="text-[10px] text-slate-400 truncate mt-1" x-text="chat.last_message || 'لا توجد رسائل بعد'"></p>
                        </div>
                        <div class="hidden group-hover:flex items-center gap-1 shrink-0">
                            <button @click.stop="renameChat(chat)" title="إعادة تسمية" class="p-1.5 rounded-lg text-slate-400 hover:text-gold-600 hover:bg-gold-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button @click.stop="deleteChat(chat)" title="حذف المحادثة" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </template>

                <div x-show="!chatsByType[activePanel].length" class="text-center py-12 text-slate-400">
                    <p class="font-bold">لا توجد سجلات حالياً</p>
                    <button @click="createNew()" class="mt-4 text-gold-600 text-sm underline">ابدأ الآن</button>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col bg-white/50 relative">
            <template x-if="selectedChat">
                <div class="h-full flex flex-col">
                    <div class="p-4 border-b border-slate-100/50 bg-white/70 backdrop-blur flex items-center justify-between">
                        <div class="flex items-center gap-3 text-right">
                            <div class="w-10 h-10 bg-gold-100 rounded-2xl flex items-center justify-center text-gold-600 font-black" x-text="selectedChat.avatar || '💬'"></div>
                            <div>
                                <h3 class="font-black text-slate-900" x-text="selectedChat.name || selectedChat.subject"></h3>
                                <p class="text-[10px] text-green-500 font-bold" x-text="activePanel === 'ai' ? 'متصل (Groq AI)' : 'فريق الدعم متاح'"></p>
                            </div>
                        </div>
                    </div>

                    <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4 flex flex-col-reverse">
                        <div x-show="isTyping" class="flex justify-start">
                            <div class="bg-slate-100 p-3 rounded-2xl rounded-tl-none animate-pulse text-xs font-bold text-slate-500">جاري الكتابة...</div>
                        </div>
                        <template x-for="msg in selectedMessages" :key="msg.id">
                            <div :class="msg.sender_type === 'user' ? 'flex justify-end' : 'flex justify-start'" class="group/msg flex items-end gap-2">
                                <button x-show="activePanel === 'ai'" @click="deleteMessage(msg)" title="حذف الرسالة"
                                        class="opacity-0 group-hover/msg:opacity-100 p-1 rounded-lg text-slate-300 hover:text-red-600 hover:bg-red-50 transition-all shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                <div class="max-w-[75%] p-4 rounded-3xl shadow-sm" :class="msg.sender_type === 'user' ? 'bg-gold-600 text-white rounded-tr-none' : 'bg-white border border-slate-100 rounded-tl-none text-slate-800'">
                                    <p class="text-sm leading-relaxed whitespace-pre-wrap" x-text="msg.message_text"></p>
        <p class="text-[9px] opacity-60 mt-2 text-left" x-text="msg.created_at || new Date(msg.created_at * 1000)?.toLocaleTimeString('ar-EG') || 'الآن'"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="p-6 bg-white/80 border-t border-slate-100/50">
                        <div class="flex items-end gap-3">
                            <textarea x-model="newMessage" @keydown.enter.exact.prevent="sendMessage"
                                     rows="1" class="flex-1 resize-none bg-slate-50 border-0 rounded-2xl px-5 py-3 focus:ring-2 focus:ring-gold-500 transition-all"
                                     placeholder="اكتب رسالتك هنا..."></textarea>
                            <button @click="sendMessage" :disabled="!newMessage.trim() || isTyping" class="w-12 h-12 bg-gold-600 text-white rounded-2xl flex items-center justify-center hover:shadow-lg disabled:opacity-50 transition-all">
                                <svg class="w-6 h-6 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="!selectedChat" class="flex-1 flex flex-col items-center justify-center text-slate-300">
                <div class="text-6xl mb-4 opacity-20">💬</div>
                <p class="font-bold">اختر محادثة من القائمة للبدء</p>
            </div>
        </div>
    </div>
</div>

<script>
function communicationHub() {
    return {
        activePanel: 'ai',
        selectedChat: null,
        selectedMessages: [],
        newMessage: '',
        isTyping: false,
        chatsByType: { ai: [], support: [] },
        panelTitles: { ai: 'مساعد الذكاء الاصطناعي', support: 'تذاكر الدعم الفني' },

        async init() {
    await this.loadChats();
    if (window.Echo) {
        // تأكد من استخدام userId الصحيح الممرر من الـ Blade
        Echo.private(`App.Models.User.${window.userId}`)
            .listen('ChatMessageSent', (e) => {
                console.log('New Message Received:', e.message); // للتحقق في الـ Console
                const incomingMsg = e.message;

                // تحديث الرسائل إذا كانت المحادثة مفتوحة
                if (this.selectedChat && incomingMsg.messageable_id == this.selectedChat.id) {
                    // نستخدم unshift لأنك تستخدم flex-col-reverse
                    this.selectedMessages.unshift(incomingMsg);
                }
                // تحديث القائمة الجانبية لظهور آخر رسالة
                this.loadChats();
            });
    }

    // ✅ Polling احتياطي لردود الدعم الفني (في حال تأخر أو فشل اتصال الـ WebSocket)
    this.pollInterval = setInterval(() => {
        if (this.selectedChat && this.activePanel === 'support') this.pollMessages();
    }, 3000);
},

async pollMessages() {
    try {
        const type = this.selectedChat.type || 'ticket';
        const res = await fetch(`/api/communications/${this.selectedChat.id}/${type}/messages`);
        const data = await res.json();
        // /messages يعيد الرسائل الأحدث أولاً (نفس ترتيب selectedMessages الحالي)
        const incoming = data.messages || [];
        const existingIds = new Set(this.selectedMessages.map(m => m.id));
        const newOnes = incoming.filter(m => !existingIds.has(m.id));
        if (newOnes.length) {
            this.selectedMessages.unshift(...newOnes);
        }
    } catch (e) {
        console.error('Poll messages error', e);
    }
},

        async loadChats() {
            const res = await fetch('/api/communications/chats');
            const data = await res.json();
            this.chatsByType.ai = data.ai_chats || [];
            this.chatsByType.support = data.tickets || data.support_chats || [];
        },

        async switchPanel(panel) {
            this.activePanel = panel;
            this.selectedChat = null;
            this.selectedMessages = [];
        },

        async selectChat(chat) {
            this.selectedChat = chat;
            const type = chat.type || (this.activePanel === 'ai' ? 'room' : 'ticket');
            const res = await fetch(`/api/communications/${chat.id}/${type}/messages`);
            const data = await res.json();
            this.selectedMessages = data.messages || [];
        },

        async createNew() {
            // الاسم بيتحدد تلقائياً من أول رسالة تُرسل في المحادثة (بدل الطلب من المستخدم كتابة اسم يدوياً)
            const token = document.querySelector('meta[name="csrf-token"]').content;
            const url = this.activePanel === 'ai' ? '/api/communications/ai/new-chat' : '/api/communications/tickets/create';
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            });
            if (!res.ok) throw new Error('Failed to create chat');
            const data = await res.json();
            await this.loadChats();
            this.selectChat(data.chat);
            this.selectedMessages = [];
        },

        async renameChat(chat) {
            const currentName = chat.name || chat.subject || '';
            const newName = window.prompt('الاسم الجديد:', currentName);
            if (newName === null || !newName.trim() || newName.trim() === currentName) return;

            const token = document.querySelector('meta[name="csrf-token"]').content;
            try {
                const res = await fetch(`/api/communications/${chat.id}/${chat.type}/rename`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ name: newName.trim() })
                });
                const data = await res.json();
                if (data.success) {
                    await this.loadChats();
                    if (this.selectedChat?.id === chat.id) this.selectedChat = data.chat;
                } else {
                    alert('تعذّر تغيير الاسم');
                }
            } catch (e) {
                console.error('Rename error', e);
                alert('تعذّر تغيير الاسم');
            }
        },

        async deleteChat(chat) {
            if (!confirm('هل أنت متأكد من حذف هذه المحادثة نهائياً؟ سيتم حذف كل رسائلها ولا يمكن التراجع.')) return;

            const token = document.querySelector('meta[name="csrf-token"]').content;
            try {
                const res = await fetch(`/api/communications/${chat.id}/${chat.type}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token }
                });
                const data = await res.json();
                if (data.success) {
                    if (this.selectedChat?.id === chat.id) {
                        this.selectedChat = null;
                        this.selectedMessages = [];
                    }
                    await this.loadChats();
                } else {
                    alert('تعذّر حذف المحادثة');
                }
            } catch (e) {
                console.error('Delete chat error', e);
                alert('تعذّر حذف المحادثة');
            }
        },

        async deleteMessage(msg) {
            if (!this.selectedChat || !confirm('حذف هذه الرسالة؟')) return;

            const token = document.querySelector('meta[name="csrf-token"]').content;
            try {
                const res = await fetch(`/api/communications/${this.selectedChat.id}/${this.selectedChat.type}/messages/${msg.id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token }
                });
                const data = await res.json();
                if (data.success) {
                    this.selectedMessages = this.selectedMessages.filter(m => m.id !== msg.id);
                } else {
                    alert('تعذّر حذف الرسالة');
                }
            } catch (e) {
                console.error('Delete message error', e);
                alert('تعذّر حذف الرسالة');
            }
        },

        async sendMessage() {
    if (!this.newMessage.trim() || this.isTyping) return;
    
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const textToSend = this.newMessage;
    
    // 1. إضافة رسالة المستخدم للواجهة فوراً
    const userMsg = { 
        id: Date.now(), 
        message_text: textToSend, 
        sender_type: 'user', 
        created_at: new Date().toLocaleTimeString('ar-EG')
    };
    this.selectedMessages.unshift(userMsg);
    this.newMessage = '';
    
    if (this.activePanel === 'ai') this.isTyping = true;

    try {
        const type = this.selectedChat.type || (this.activePanel === 'ai' ? 'room' : 'ticket');
        const res = await fetch(`/api/communications/${this.selectedChat.id}/${type}/send`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': token 
            },
            body: JSON.stringify({ message: textToSend })
        });

        const data = await res.json();
        this.isTyping = false;

        if (data.success) {
            // 2. إضافة رد الذكاء الاصطناعي للمصفوفة
            if (data.messages) {
                data.messages.forEach(msg => {
                    // نتجنب تكرار رسالة المستخدم التي أضفناها يدوياً فوق
                    if (msg.sender_type !== 'user') {
                        this.selectedMessages.unshift(msg);
                    }
                });
            }

            // 2.5 تحديث اسم المحادثة تلقائياً إذا كانت هذه أول رسالة (السيرفر بيولّد عنوان مفهوم من نص الرسالة)
            if (data.chat_name && this.selectedChat) {
                if (this.selectedChat.type === 'ticket') {
                    this.selectedChat.subject = data.chat_name;
                } else {
                    this.selectedChat.name = data.chat_name;
                }
                this.loadChats();
            }

            // 3. منطق التحويل التلقائي للدعم الفني
            if (data.redirect_to_support) {
                setTimeout(async () => {
                    // تحديث القوائم أولاً لجلب التذكرة الجديدة
                    await this.loadChats();
                    
                    // الانتقال للوحة الدعم الفني
                    this.activePanel = 'support'; 
                    
                    // اختيار أول تذكرة (التي تم إنشاؤها للتو)
                    if (this.chatsByType.support.length > 0) {
                        this.selectChat(this.chatsByType.support[0]);
                    }
                    
                    alert('تم فتح تذكرة دعم فني لك تلقائياً وتحويلك إليها.');
                }, 1500); 
            }
        }
    } catch (e) {
        console.error('Send error:', e);
        this.isTyping = false;
    }
},

        statusColor(status) {
            const colors = { 'open': 'bg-green-100 text-green-700', 'pending': 'bg-yellow-100 text-yellow-700', 'closed': 'bg-slate-100 text-slate-700' };
            return colors[status] || 'bg-gold-100 text-gold-700';
        }
    }
}
</script>
@endsection