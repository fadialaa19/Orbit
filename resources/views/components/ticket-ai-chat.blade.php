@props(['ticketId'])

<div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 space-y-3 mt-4">
    <div class="flex items-center gap-2">
        <div class="w-6 h-6 bg-indigo-600 rounded-xl flex items-center justify-center text-white text-xs font-black">AI</div>
        <p class="text-sm font-bold text-indigo-800">الذكاء الاصطناعي جاهز للمساعدة</p>
    </div>
    <div class="flex gap-2">
        <input type="text" x-model="aiMessage" placeholder="اسأل الذكاء الاصطناعي..." 
               class="flex-1 bg-white border border-indigo-200 rounded-xl px-4 py-2.5 text-sm font-bold outline-none focus:border-indigo-500 focus:bg-indigo-50 transition-all"
               @keydown.enter.prevent="sendAI" :disabled="aiLoading">
        <button @click="sendAI" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-xs font-black hover:bg-indigo-700 transition disabled:opacity-50" :disabled="aiLoading || !aiMessage.trim()">
            <span x-show="!aiLoading">إرسال</span>
            <span x-show="aiLoading">⏳</span>
        </button>
    </div>
    <template x-for="aiReply in aiReplies" :key="aiReply.id">
        <div class="p-3 bg-white rounded-2xl border border-indigo-100 text-xs font-bold text-indigo-900">
            <p x-text="aiReply.message_text" class="whitespace-pre-line mb-1"></p>
            <p class="text-[9px] text-indigo-500" x-text="aiReply.created_at"></p>
        </div>
    </template>
    <p x-show="aiLoading" class="text-xs text-indigo-500 text-center">جاري التفكير...</p>
</div>

<script>
return {
    aiMessage: '',
    aiReplies: [],
    aiLoading: false,
    
    async sendAI() {
        const text = this.aiMessage.trim();
        if (!text) return;
        
        this.aiLoading = true;
        const formData = new FormData();
        formData.append('message', text);
        
        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch(`/tickets/{{ $ticketId }}/ai-reply`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                this.aiMessage = '';
                this.aiReplies.push(data.message);
                // Reload main messages
                window.studentChatComponent?.loadMessages();
            } else {
                alert(data.message || 'خطأ');
            }
        } catch (e) {
            console.error(e);
            alert('خطأ في الاتصال');
        }
        this.aiLoading = false;
    }
}
</script>

