<div x-data="aiSections()" x-show="show" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-8 border-b border-slate-200">
            <h3 class="text-2xl font-black text-slate-800 flex items-center gap-3">
                <span>✨</span>
                تم توليد الأقسام بالذكاء الاصطناعي
            </h3>
        </div>

        <div class="p-8 space-y-6">
            <template x-for="(content, section) in sections" :key="section">
                <div class="border border-slate-100 rounded-2xl p-6 hover:shadow-lg transition-all bg-gradient-to-r from-slate-50/50">
                    <div class="flex items-center gap-3 mb-4">
                        <span x-text="getIcon(section)" class="text-2xl"></span>
                        <h4 x-text="getTitle(section)" class="font-black text-lg text-slate-800"></h4>
                    </div>
                    <textarea 
                        x-model="sections[section]" 
                        :name="section"
                        rows="8" 
                        class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:border-gold-400 focus:ring-2 focus:ring-gold-100 resize-vertical transition-all placeholder-slate-400"
                        placeholder="المحتوى سيتم ملؤه هنا..."></textarea>
                </div>
            </template>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6 bg-slate-50 rounded-2xl border border-slate-200">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase mb-2 block">القيمة المالية</label>
                    <input type="text" x-model="financial_value" name="financial_value" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase mb-2 block">عدد المتقدمين</label>
                    <input type="number" x-model="applicants_count" name="applicants_count" min="0" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase mb-2 block">الكلمات الموصى بها</label>
                    <input type="text" x-model="recommended_tags_str" name="recommended_tags" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold text-left">
                </div>
            </div>

            <div class="flex gap-4 pt-6 border-t border-slate-200">
                <button @click="insertSections()" class="flex-1 bg-emerald-500 text-white py-4 rounded-2xl font-black text-sm hover:bg-emerald-600 shadow-lg shadow-emerald-100 transition-all">
                    إدراج في النموذج وإغلاق
                </button>
                <button @click="show = false" class="px-8 py-4 rounded-2xl font-black text-sm text-slate-500 hover:text-slate-700 border border-slate-200 hover:bg-slate-50 transition-all">
                    إغلاق
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function aiSections() {
    return {
        show: false,
        sections: {},
        financial_value: '',
        applicants_count: 0,
        recommended_tags_str: '',
        
        getIcon(section) {
            const icons = {
                'overview': '📖',
                'conditions': '✅',
                'documents': '📄',
                'features': '⭐'
            };
            return icons[section] || '📄';
        },
        
        getTitle(section) {
            const titles = {
                'overview': 'نظرة عامة',
                'conditions': 'الشروط',
                'documents': 'المستندات المطلوبة',
                'features': 'المميزات'
            };
            return titles[section] || section;
        },

        async generateAllSections() {
            const title = document.getElementById('title_ar').value;
            if (!title) {
                alert('أدخل عنوان المنحة أولاً!');
                return;
            }

            const btn = document.getElementById('aiGenerateBtn');
            const spinner = document.getElementById('aiIcon');
            const text = document.getElementById('aiText');
            
            btn.disabled = true;
            spinner.classList.add('animate-spin');
            text.innerText = 'جاري التوليد...';

            try {
                const response = await fetch('{{ route('admin.scholarships.generate-sections') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ title_ar: title })
                });

                const data = await response.json();

                if (data.sections) {
                    this.sections = data.sections;
                    this.financial_value = data.financial_value;
                    this.applicants_count = data.applicants_count;
                    this.recommended_tags_str = data.recommended_tags.join(', ');
                    this.show = true;
                } else {
                    alert(data.error || 'حدث خطأ');
                }
            } catch (error) {
                console.error(error);
                alert('خطأ في الاتصال');
            } finally {
                btn.disabled = false;
                spinner.classList.remove('animate-spin');
                text.innerText = 'توليد الأقسام';
            }
        },

        insertSections() {
            // Fill form fields
            Object.keys(this.sections).forEach(section => {
                const field = document.querySelector(`[name="${section}"]`);
                if (field) field.value = this.sections[section];
            });
            
            document.querySelector('[name="financial_value"]').value = this.financial_value;
            document.querySelector('[name="applicants_count"]').value = this.applicants_count;
            document.querySelector('[name="recommended_tags"]').value = this.recommended_tags_str;
            
            this.show = false;
        }
    }
}
</script>
