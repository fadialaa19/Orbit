<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد البريد الإلكتروني - Orbit ☕️</title>
    <style>
        body { font-family: 'Cairo', sans-serif; margin: 0; padding: 0; background: #f8fafc; }
        .container { max-width: 680px; margin: 0 auto; padding: 24px; }
        .card { background: #ffffff; border-radius: 18px; padding: 28px; box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08); border: 1px solid #eef2ff; }
        .brand { display: flex; justify-content: center; margin-bottom: 14px; }
        .brand-inner { width: 64px; height: 64px; background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%); border-radius: 18px; display: flex; align-items: center; justify-content: center; box-shadow: 0 12px 30px rgba(99,102,241,0.25); margin: 0 auto; }
        .brand-inner svg { width: 28px; height: 28px; color: #fff; }
        h1 { margin: 15px 0 8px 0; font-size: 22px; font-weight: 900; color: #0f172a; text-align: center; }
        p { margin: 0 0 16px 0; color: #64748b; line-height: 1.7; }
        .content { text-align: center; }
        .box { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 14px; padding: 14px 16px; margin: 18px 0; }
        .btn { display: inline-block; background: #4f46e5; color: #ffffff !important; text-decoration: none; padding: 12px 32px; border-radius: 14px; font-weight: 800; font-size: 16px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
        .fine { font-size: 12.5px; color: #94a3b8; }
        .footer { margin-top: 18px; text-align: center; font-size: 12.5px; color: #94a3b8; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="brand">
            <div class="brand-inner">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>

        <div class="content">
            <h1>تأكيد البريد الإلكتروني</h1>
            <p>مرحباً {{ $name ?? 'عزيزنا المستخدم' }}</p>
            <p>اضغط على الزر أدناه لتأكيد بريدك الإلكتروني والبدء في استخدام الحساب.</p>

            <div style="margin: 24px 0;">
                @if(!empty($verificationUrl))
                    <a class="btn" href="{{ $verificationUrl }}" target="_blank" rel="noopener noreferrer">تأكيد البريد الإلكتروني</a>
                @else
                    <p style="color: #ef4444;">حدث خطأ في توليد رابط التأكيد، يرجى إعادة المحاولة.</p>
                @endif
            </div>

            <div class="box">
                <p style="margin:0;">إذا لم تكن أنت من قام بالتسجيل في Orbit ☕️، يمكنك تجاهل هذه الرسالة بأمان.</p>
            </div>

            <p class="fine" style="margin-top:14px;">
                هذا الرابط صالح لمدة 60 دقيقة فقط من وقت إرساله.
            </p>
        </div>

        <div class="footer">
            © {{ date('Y') }} Orbit ☕️. جميع الحقوق محفوظة.
        </div>
    </div>
</div>
</body>
</html>