<div x-data="{ show: false, message: '', type: 'success', timeout: null }" 
     x-init="$watch('$store.toast.show', value => { 
         if(value) { 
             show = true; 
             clearTimeout(timeout); 
             timeout = setTimeout(() => show = false, 5000); 
         } 
     })"
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-x-full"
     x-transition:enter-end="opacity-100 transform translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-x-0"
     x-transition:leave-end="opacity-0 transform translate-x-full"
     :class="type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-gold-500'"
     class="fixed top-20 right-6 z-50 p-4 rounded-2xl text-white shadow-2xl max-w-sm font-bold text-sm flex items-center gap-3">
    <svg x-show="type === 'success'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
    </svg>
    <svg x-show="type === 'error'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
    </svg>
    <span x-text="message"></span>
    <button @click="show = false" class="ml-auto text-white hover:opacity-75">&times;</button>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('toast', {
        show: false,
        type: 'success',
        message: '',
        showToast(type, message) {
            this.type = type;
            this.message = message;
            this.show = true;
            setTimeout(() => this.show = false, 5000);
        }
    });
});
</script>
