@if (session('success'))
<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-[1.5rem] mb-6" role="alert">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span class="font-bold ml-3">{{ session('success') }}</span>
    </div>
</div>
@endif

@if (session('error'))
<div class="bg-rose-50 border border-rose-200 text-rose-800 px-6 py-4 rounded-[1.5rem] mb-6" role="alert">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <span class="font-bold ml-3">{{ session('error') }}</span>
    </div>
</div>
@endif

