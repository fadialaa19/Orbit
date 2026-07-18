@extends('layouts.dashboard')
@section('title', 'المجتمع')

@section('content')
<div class="max-w-7xl mx-auto h-dashboard-screen flex bg-gradient-to-b from-slate-50 to-white" dir="rtl"
     x-data="communityHub()" x-init="init()">

    {{-- قائمة المجتمعات --}}
    <div class="w-full lg:w-80 border-l border-slate-100/50 bg-gradient-to-b from-slate-50/50 backdrop-blur-sm flex flex-col"
         x-show="!selectedCommunity || !isMobile()" x-cloak>
        <div class="p-5 border-b border-slate-100/50 bg-white/70">
            <h2 class="text-xl font-black text-slate-900">المجتمع</h2>
            <p class="text-[11px] text-slate-400 font-bold mt-1">تواصل مع باقي طلاب أوربيت</p>
        </div>

        <div class="overflow-y-auto flex-1">
            <template x-for="community in communities" :key="community.id">
                <button @click="selectCommunity(community)"
                        :class="selectedCommunity?.id === community.id ? 'bg-white shadow-lg border-r-4 border-gold-500' : 'hover:bg-white/50 border-r-4 border-transparent'"
                        class="w-full flex items-center gap-4 p-4 transition-all text-right">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg shrink-0 overflow-hidden bg-gradient-to-r from-gold-500 to-gold-600">
                        <img x-show="community.image" :src="community.image" class="w-full h-full object-cover">
                        <span x-show="!community.image" x-text="community.icon"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-black text-slate-900 text-sm truncate" x-text="community.name"></p>
                            <span class="text-[8px] font-black px-2 py-0.5 rounded-full shrink-0"
                                  :class="community.type === 'announcement' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'"
                                  x-text="community.type === 'announcement' ? 'تعليمات' : 'نقاش'"></span>
                        </div>
                        <p class="text-[10px] text-slate-400 truncate mt-1" x-text="community.last_message || 'لا توجد رسائل بعد'"></p>
                    </div>
                </button>
            </template>

            <div x-show="!communities.length" class="text-center py-12 text-slate-400">
                <p class="font-bold text-sm">لا توجد مجتمعات متاحة حالياً</p>
            </div>
        </div>
    </div>

    {{-- المحادثة --}}
    <div class="flex-1 flex flex-col bg-white/50 relative" x-show="selectedCommunity || !isMobile()" x-cloak>
        <template x-if="selectedCommunity">
            <div class="h-full flex flex-col">
                {{-- هيدر المجتمع --}}
                <div class="p-4 border-b border-slate-100/50 bg-white/70 backdrop-blur flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 text-right min-w-0">
                        <button @click="selectedCommunity = null" class="lg:hidden text-slate-400 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <div class="w-10 h-10 bg-gold-100 rounded-2xl flex items-center justify-center text-lg shrink-0 overflow-hidden">
                            <img x-show="selectedCommunity.image" :src="selectedCommunity.image" class="w-full h-full object-cover">
                            <span x-show="!selectedCommunity.image" x-text="selectedCommunity.icon"></span>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-black text-slate-900 truncate" x-text="selectedCommunity.name"></h3>
                            <p class="text-[10px] text-slate-400 font-bold truncate" x-text="selectedCommunity.description || (selectedCommunity.type === 'announcement' ? 'تعليمات ومنشورات الإدارة' : 'نقاش مفتوح بين الطلاب')"></p>
                        </div>
                    </div>
                </div>

                {{-- قواعد المجتمع --}}
                <div x-show="showGuidelines" x-cloak class="bg-gold-50 border-b border-gold-100 px-5 py-3 flex items-center justify-between gap-3">
                    <p class="text-[11px] font-bold text-gold-800">
                        🤝 هذا مجتمع أوربيت: نحترم بعض دايمًا، ونحافظ على أجواء هادئة وآمنة لكل الطلاب. أي إساءة بتترصد وبيتم التعامل معها فورًا.
                    </p>
                    <button @click="dismissGuidelines()" class="text-gold-500 hover:text-gold-700 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- الرسالة المثبتة --}}
                <div x-show="pinnedMessage" x-cloak class="bg-amber-50 border-b border-amber-100 px-5 py-3 flex items-start justify-between gap-3">
                    <div class="flex items-start gap-2 min-w-0">
                        <span class="text-amber-500 shrink-0">📌</span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-black text-amber-600" x-text="'مثبّتة من ' + (pinnedMessage?.sender_name || '')"></p>
                            <p class="text-xs font-bold text-amber-800 truncate" x-text="pinnedMessage?.message_text"></p>
                        </div>
                    </div>
                    <button x-show="isAdmin" @click="unpinMessage()" class="text-[10px] font-black text-amber-500 hover:text-amber-700 shrink-0">إلغاء التثبيت</button>
                </div>

                {{-- الرسائل --}}
                <div id="community-messages" class="flex-1 overflow-y-auto p-3 sm:p-6 space-y-2.5 sm:space-y-4 bg-slate-50/30">
                    <template x-for="msg in messages" :key="msg.id">
                        <div class="group/msg flex gap-2 sm:gap-3 items-start" :class="msg.sender_id === currentUserId ? 'flex-row-reverse' : ''">
                            <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-xl flex items-center justify-center text-white text-[9px] sm:text-[11px] font-black shrink-0 overflow-hidden"
                                 :class="msg.sender_type === 'admin' ? 'bg-navy-900' : 'bg-gold-600'">
                                <img x-show="msg.sender_avatar" :src="msg.sender_avatar" class="w-full h-full object-cover">
                                <span x-show="!msg.sender_avatar" x-text="(msg.sender_name || '؟').charAt(0)"></span>
                            </div>

                            <div class="max-w-[82%] sm:max-w-[75%]">
                                <div class="flex items-center gap-1.5 sm:gap-2 mb-1" :class="msg.sender_id === currentUserId ? 'flex-row-reverse' : ''">
                                    <span class="text-[9px] sm:text-[10px] font-black text-slate-500" x-text="msg.sender_name"></span>
                                    <span x-show="msg.sender_type === 'admin'" class="text-[7px] sm:text-[8px] font-black bg-navy-900 text-white px-1.5 py-0.5 rounded">الإدارة</span>
                                    <span x-show="msg.is_pinned" class="text-[9px] sm:text-[10px]">📌</span>
                                </div>

                                <template x-if="!msg.is_removed">
                                    <div class="p-2 sm:p-3 rounded-2xl text-[11px] sm:text-xs font-bold leading-relaxed shadow-sm"
                                         :class="msg.sender_id === currentUserId ? 'bg-gold-600 text-white rounded-tr-sm' : 'bg-white text-slate-700 rounded-tl-sm border border-slate-100'">
                                        <div x-show="msg.reply_to" x-cloak class="mb-2 px-2.5 py-1.5 rounded-xl text-[9px] sm:text-[10px] font-bold border-r-2 opacity-90"
                                             :class="msg.sender_id === currentUserId ? 'bg-white/10 border-white/40' : 'bg-slate-50 border-gold-400 text-slate-500'">
                                            <p class="opacity-80" x-text="msg.reply_to?.sender_name"></p>
                                            <p class="truncate" x-text="msg.reply_to?.message_text"></p>
                                        </div>
                                        <p x-text="msg.message_text" class="whitespace-pre-line"></p>
                                    </div>
                                </template>
                                <template x-if="msg.is_removed">
                                    <div class="p-2 sm:p-3 rounded-2xl text-[10px] sm:text-[11px] font-bold italic text-slate-400 bg-slate-100 border border-slate-200 border-dashed">
                                        🚫 تم حذف هذه الرسالة من قبل الإدارة
                                    </div>
                                </template>

                                <p class="text-[8px] sm:text-[9px] text-slate-300 font-bold mt-1" :class="msg.sender_id === currentUserId ? 'text-left' : 'text-right'" x-text="msg.created_at"></p>
                            </div>

                            {{-- أدوات الرد والإدارة --}}
                            <div x-show="!msg.is_removed" class="opacity-0 group-hover/msg:opacity-100 transition-all flex items-center gap-1 shrink-0">
                                <button @click="startReply(msg)" title="رد" class="p-1.5 rounded-lg text-slate-300 hover:text-gold-600 hover:bg-gold-50">↩️</button>
                                <template x-if="isAdmin">
                                    <div class="flex items-center gap-1">
                                        <button @click="pinMessage(msg)" title="تثبيت" class="p-1.5 rounded-lg text-slate-300 hover:text-amber-600 hover:bg-amber-50">📌</button>
                                        <button x-show="msg.sender_type !== 'admin'" @click="openMuteModal(msg)" title="كتم العضو" class="p-1.5 rounded-lg text-slate-300 hover:text-rose-600 hover:bg-rose-50">🔇</button>
                                        <button @click="deleteMessage(msg)" title="حذف الرسالة" class="p-1.5 rounded-lg text-slate-300 hover:text-rose-600 hover:bg-rose-50">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <div x-show="!messages.length" class="text-center py-16 text-slate-300">
                        <div class="text-5xl mb-3">💬</div>
                        <p class="font-bold text-sm">لا توجد رسائل بعد، كن أول من يبدأ الحديث</p>
                    </div>
                </div>

                {{-- الإدخال --}}
                <div class="p-4 bg-white border-t border-slate-100 shrink-0">
                    <template x-if="canPost && !mutedUntil">
                        <div>
                            <div x-show="replyingTo" x-cloak class="flex items-center justify-between gap-2 bg-slate-50 border border-slate-100 rounded-xl px-3 py-2 mb-2">
                                <div class="min-w-0">
                                    <p class="text-[10px] font-black text-gold-600">↩️ رد على <span x-text="replyingTo?.sender_name"></span></p>
                                    <p class="text-[11px] font-bold text-slate-500 truncate" x-text="replyingTo?.message_text"></p>
                                </div>
                                <button @click="replyingTo = null" class="text-slate-300 hover:text-slate-500 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <form @submit.prevent="sendMessage()" class="flex gap-2">
                                <input type="text" x-model="newMessage" placeholder="اكتب رسالتك بكل احترام..."
                                       class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold outline-none focus:border-gold-500 focus:bg-white transition-all"
                                       :disabled="loading">
                                <button type="submit" class="bg-gold-600 text-white w-11 h-11 rounded-xl flex items-center justify-center hover:bg-gold-700 transition disabled:opacity-50"
                                        :disabled="loading || !newMessage.trim()">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </button>
                            </form>
                        </div>
                    </template>

                    <template x-if="mutedUntil">
                        <div class="bg-rose-50 border border-rose-100 rounded-xl p-3 text-center">
                            <p class="text-xs font-black text-rose-600">🔇 تم تقييد إرسال الرسائل عنك مؤقتاً في هذا المجتمع</p>
                            <p class="text-[10px] font-bold text-rose-400 mt-1" x-text="'حتى: ' + formatDate(mutedUntil)"></p>
                        </div>
                    </template>

                    <template x-if="!canPost && !mutedUntil">
                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 text-center">
                            <p class="text-xs font-black text-slate-400">📢 هذا المجتمع مخصص لمنشورات الإدارة فقط</p>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <div x-show="!selectedCommunity" class="flex-1 flex flex-col items-center justify-center text-slate-300">
            <div class="text-6xl mb-4 opacity-20">🌐</div>
            <p class="font-bold">اختر مجتمع من القائمة للانضمام للنقاش</p>
        </div>
    </div>

    {{-- مودال الكتم --}}
    <div x-show="muteModalOpen" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4">
        <div class="bg-white rounded-[2rem] w-full max-w-sm p-6 shadow-2xl" @click.away="muteModalOpen = false">
            <h3 class="text-sm font-black text-slate-900 mb-1">كتم العضو</h3>
            <p class="text-[11px] font-bold text-slate-400 mb-4" x-text="'سيتم تقييد ' + (muteTarget?.sender_name || '') + ' من إرسال رسائل جديدة في هذا المجتمع'"></p>

            <div class="grid grid-cols-2 gap-2 mb-4">
                <template x-for="opt in muteDurationOptions" :key="opt.value">
                    <button type="button" @click="muteDuration = opt.value"
                            :class="muteDuration === opt.value ? 'bg-gold-600 text-white' : 'bg-slate-50 text-slate-600 border border-slate-100'"
                            class="px-3 py-2 rounded-xl text-xs font-black transition-all" x-text="opt.label"></button>
                </template>
            </div>

            <textarea x-model="muteReason" placeholder="سبب الكتم (اختياري)" rows="2"
                      class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold outline-none focus:border-gold-500 mb-4"></textarea>

            <div class="flex gap-2">
                <button @click="confirmMute()" class="flex-1 bg-rose-600 text-white py-2.5 rounded-xl text-xs font-black hover:bg-rose-700 transition">تأكيد الكتم</button>
                <button @click="muteModalOpen = false" class="px-4 bg-slate-100 text-slate-500 py-2.5 rounded-xl text-xs font-black hover:bg-slate-200 transition">إلغاء</button>
            </div>
        </div>
    </div>
</div>

<script>
function communityHub() {
    return {
        communities: [],
        selectedCommunity: null,
        messages: [],
        pinnedMessage: null,
        newMessage: '',
        replyingTo: null,
        loading: false,
        isAdmin: false,
        mutedUntil: null,
        canPost: true,
        currentUserId: {{ auth()->id() }},
        showGuidelines: true,
        pollInterval: null,
        echoSubscribed: null,
        muteModalOpen: false,
        muteTarget: null,
        muteDuration: '1d',
        muteReason: '',
        muteDurationOptions: [
            { value: '1h', label: 'ساعة واحدة' },
            { value: '1d', label: 'يوم واحد' },
            { value: '3d', label: '3 أيام' },
            { value: '7d', label: 'أسبوع' },
            { value: 'permanent', label: 'دائم' },
        ],

        init() {
            this.showGuidelines = localStorage.getItem('community_guidelines_dismissed') !== 'true';
            this.loadCommunities();
            this.pollInterval = setInterval(() => {
                if (this.selectedCommunity) this.pollMessages();
            }, 4000);
        },

        isMobile() {
            return window.innerWidth < 1024;
        },

        async loadCommunities() {
            try {
                const res = await fetch('{{ route('community.index') }}');
                const data = await res.json();
                this.communities = data.communities || [];

                // دعم الفتح المباشر من رابط لوحة الإدارة: ?open=ID
                const openId = new URLSearchParams(window.location.search).get('open');
                if (openId) {
                    const target = this.communities.find(c => c.id == openId);
                    if (target) this.selectCommunity(target);
                }
            } catch (e) { console.error('Load communities error', e); }
        },

        async selectCommunity(community) {
            if (this.echoSubscribed && window.Echo) {
                window.Echo.leave('community.' + this.echoSubscribed);
            }
            this.selectedCommunity = community;
            this.messages = [];
            this.pinnedMessage = null;
            await this.loadMessages();
            this.subscribeToEcho();
        },

        async loadMessages() {
            try {
                const res = await fetch(`/api/community/${this.selectedCommunity.id}/messages`);
                const data = await res.json();
                this.messages = data.messages || [];
                this.pinnedMessage = data.pinned_message;
                this.isAdmin = data.is_admin;
                this.mutedUntil = data.muted_until;
                this.canPost = data.can_post;
                this.scrollToBottom();
            } catch (e) { console.error('Load messages error', e); }
        },

        subscribeToEcho() {
            if (!window.Echo || typeof window.Echo.private !== 'function') return;
            const communityId = this.selectedCommunity.id;
            window.Echo.private('community.' + communityId)
                .listen('CommunityMessageSent', (e) => {
                    if (!this.selectedCommunity || this.selectedCommunity.id !== communityId) return;
                    if (this.messages.some(m => m.id === e.message.id)) return;
                    this.messages.push({
                        id: e.message.id,
                        sender_id: e.message.sender?.id,
                        sender_name: e.message.sender?.name || 'مستخدم',
                        sender_avatar: e.message.sender?.avatar || null,
                        sender_type: e.message.sender_type,
                        message_text: e.message.message_text,
                        reply_to: e.message.reply_to || null,
                        is_removed: false,
                        is_pinned: false,
                        created_at: new Date(e.message.created_at).toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' }),
                    });
                    this.scrollToBottom();
                });
            this.echoSubscribed = communityId;
        },

        async pollMessages() {
            if (!this.selectedCommunity) return;
            try {
                const wasNearBottom = this.isNearBottom();
                const res = await fetch(`/api/community/${this.selectedCommunity.id}/messages`);
                const data = await res.json();
                this.messages = data.messages || [];
                this.pinnedMessage = data.pinned_message;
                this.mutedUntil = data.muted_until;
                this.canPost = data.can_post;
                if (wasNearBottom) this.scrollToBottom();
            } catch (e) { console.error('Poll messages error', e); }
        },

        startReply(msg) {
            this.replyingTo = msg;
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.loading) return;
            this.loading = true;
            const text = this.newMessage;
            const replyToId = this.replyingTo?.id || null;
            this.newMessage = '';
            this.replyingTo = null;
            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const res = await fetch(`/api/community/${this.selectedCommunity.id}/send`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({ message: text, reply_to_message_id: replyToId })
                });
                const data = await res.json();
                if (data.success) {
                    this.messages.push(data.message);
                    this.scrollToBottom();
                } else {
                    alert(data.message || 'تعذّر إرسال الرسالة');
                    this.newMessage = text;
                }
            } catch (e) {
                alert('حدث خطأ أثناء الإرسال');
                this.newMessage = text;
            }
            this.loading = false;
        },

        async deleteMessage(msg) {
            if (!confirm('حذف هذه الرسالة؟ سيظهر للأعضاء إشعار شفاف بأنها حُذفت من الإدارة.')) return;
            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                await fetch(`/api/community/${this.selectedCommunity.id}/messages/${msg.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': token }
                });
                msg.is_removed = true;
                msg.message_text = null;
            } catch (e) { console.error('Delete message error', e); }
        },

        async pinMessage(msg) {
            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                await fetch(`/api/community/${this.selectedCommunity.id}/messages/${msg.id}/pin`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token }
                });
                await this.loadMessages();
            } catch (e) { console.error('Pin message error', e); }
        },

        async unpinMessage() {
            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                await fetch(`/api/community/${this.selectedCommunity.id}/unpin`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token }
                });
                this.pinnedMessage = null;
                this.messages.forEach(m => m.is_pinned = false);
            } catch (e) { console.error('Unpin error', e); }
        },

        openMuteModal(msg) {
            this.muteTarget = msg;
            this.muteDuration = '1d';
            this.muteReason = '';
            this.muteModalOpen = true;
        },

        async confirmMute() {
            if (!this.muteTarget) return;
            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const res = await fetch(`/api/community/${this.selectedCommunity.id}/mute`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({
                        user_id: this.muteTarget.sender_id,
                        duration: this.muteDuration,
                        reason: this.muteReason || null,
                    })
                });
                const data = await res.json();
                this.muteModalOpen = false;
                if (data.success) {
                    alert('تم تقييد العضو بنجاح');
                } else {
                    alert(data.message || 'تعذّر تنفيذ الإجراء');
                }
            } catch (e) {
                console.error('Mute error', e);
                this.muteModalOpen = false;
            }
        },

        dismissGuidelines() {
            this.showGuidelines = false;
            localStorage.setItem('community_guidelines_dismissed', 'true');
        },

        formatDate(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            return d.toLocaleString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = document.getElementById('community-messages');
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

        isNearBottom() {
            const el = document.getElementById('community-messages');
            if (!el) return true;
            return el.scrollHeight - el.scrollTop - el.clientHeight < 120;
        }
    };
}
</script>
@endsection
