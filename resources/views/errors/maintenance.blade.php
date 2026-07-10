<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحسينات جارية</title>

@vite(['resources/css/app.css'])
<style>

        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;600;700&display=swap');
        body, html { 
            height: 100%; 
            margin: 0; 
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            background-color: #0F1B3D;
            overflow: hidden;
        }
        .mesh-gradient {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
            background-image:
                radial-gradient(at 0% 0%, rgba(219, 138, 71, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(59, 74, 120, 0.25) 0px, transparent 50%);
            filter: blur(80px);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="flex items-center justify-center text-white">

    <div class="mesh-gradient"></div>

    <div class="z-10 w-full max-w-3xl px-6 flex flex-col items-center text-center">
        
        <div class="mb-10">
            
                <img src="{{ asset('assets/images/logo.png') }}" class="h-12 md:h-14 mx-auto" alt="Logo">
                <h2 class="text-2xl font-black tracking-tighter italic">Orbit ☕️</h2>
            
        </div>

        <div class="mb-8">
            <h3 class="text-gold-400 font-bold tracking-[0.3em] text-[10px] md:text-xs uppercase mb-4">نظامنا يتطور الآن</h3>
            <h1 class="text-3xl md:text-5xl font-black leading-tight">
                نعمل على توفير <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-300 to-gold-400">
                    بيئة تعليمية أفضل
                </span>
            </h1>
        </div>

        <div class="glass-card w-full max-w-xl py-8 px-6 rounded-[2.5rem] shadow-2xl mb-10">
            <p class="text-slate-400 text-sm md:text-base font-light mb-8">
                " {{ $message ?? 'الموقع في استراحة قصيرة لإجراء تحديثات فنية. سنعود قريباً.' }} "
            </p>

            @php $until = \App\Models\Setting::get('maintenance_until'); @endphp
            
            <div class="flex justify-center gap-4" id="countdown" data-expire="{{ $until }}">
                <div class="bg-white/5 p-4 rounded-2xl min-w-[80px] border border-white/5">
                    <span id="hours" class="text-2xl md:text-3xl font-bold block">00</span>
                    <span class="text-[9px] uppercase text-slate-500 font-black mt-1 block">ساعة</span>
                </div>
                <div class="bg-white/5 p-4 rounded-2xl min-w-[80px] border border-white/5">
                    <span id="minutes" class="text-2xl md:text-3xl font-bold block">00</span>
                    <span class="text-[9px] uppercase text-slate-500 font-black mt-1 block">دقيقة</span>
                </div>
                <div class="bg-gold-500/10 p-4 rounded-2xl min-w-[80px] border border-gold-500/20">
                    <span id="seconds" class="text-2xl md:text-3xl font-bold block text-gold-400">00</span>
                    <span class="text-[9px] uppercase text-gold-500/50 font-black mt-1 block">ثانية</span>
                </div>
            </div>
        </div>

        <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-white/5 border border-white/5 text-slate-500 text-[10px] font-bold tracking-widest uppercase">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            نحن متصلون الآن
        </div>
    </div>

    <script>
        function updateCountdown() {
            const el = document.getElementById('countdown');
            if(!el || !el.dataset.expire) return;
            
            const expireDate = new Date(el.dataset.expire).getTime();
            const now = new Date().getTime();
            const diff = expireDate - now;

            if (diff > 0) {
                document.getElementById('hours').innerText = String(Math.floor(diff / (1000 * 60 * 60))).padStart(2, '0');
                document.getElementById('minutes').innerText = String(Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                document.getElementById('seconds').innerText = String(Math.floor((diff % (1000 * 60)) / 1000)).padStart(2, '0');
            } else {
                el.innerHTML = "<span class='text-gold-400 font-bold tracking-widest text-xs uppercase'>أوشكنا على الانتهاء</span>";
            }
        }
        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
</body>
</html>