<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منحة جديدة - Orbit</title>
    <style>
        body { font-family: 'Cairo', sans-serif; margin: 0; padding: 0; background: #f8fafc; }
        .container { max-width: 680px; margin: 0 auto; padding: 24px; }
        .card { background: #ffffff; border-radius: 18px; padding: 28px; box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08); border: 1px solid #eef2ff; }
        .brand { display: flex; justify-content: center; margin-bottom: 14px; }
        .brand-inner { width: 64px; height: 64px; background: linear-gradient(135deg, #DB8A47 0%, #E8935B 100%); border-radius: 18px; display: flex; align-items: center; justify-content: center; box-shadow: 0 12px 30px rgba(219,138,71,0.25); margin: 0 auto; font-size: 28px; }
        h1 { margin: 15px 0 8px 0; font-size: 22px; font-weight: 900; color: #0f172a; text-align: center; }
        p { margin: 0 0 16px 0; color: #64748b; line-height: 1.7; }
        .content { text-align: center; }
        .box { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 14px; padding: 16px 18px; margin: 18px 0; text-align: right; }
        .box p { margin: 0 0 6px 0; }
        .box .label { color: #94a3b8; font-size: 12.5px; font-weight: 700; }
        .box .value { color: #0f172a; font-weight: 900; font-size: 14px; }
        .btn { display: inline-block; background: #DB8A47; color: #ffffff !important; text-decoration: none; padding: 12px 32px; border-radius: 14px; font-weight: 800; font-size: 16px; box-shadow: 0 4px 12px rgba(219, 138, 71, 0.3); }
        .footer { margin-top: 18px; text-align: center; font-size: 12.5px; color: #94a3b8; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="brand">
            <div class="brand-inner">🎓</div>
        </div>

        <div class="content">
            <h1>منحة جديدة تناسبك!</h1>
            <p>مرحباً {{ $studentName }}، تم نشر منحة جديدة على منصة Orbit وقد تناسب ملفك الدراسي.</p>

            <div class="box">
                <p><span class="label">المنحة: </span><span class="value">{{ $scholarship->title_ar }}</span></p>
                <p><span class="label">الجامعة: </span><span class="value">{{ $scholarship->university }}</span></p>
                <p><span class="label">الدولة: </span><span class="value">{{ $scholarship->country }}</span></p>
                @if($scholarship->deadline)
                    <p><span class="label">آخر موعد للتقديم: </span><span class="value">{{ $scholarship->formatted_deadline }}</span></p>
                @endif
            </div>

            <div style="margin: 24px 0;">
                <a class="btn" href="{{ $scholarshipUrl }}" target="_blank" rel="noopener noreferrer">عرض تفاصيل المنحة</a>
            </div>
        </div>

        <div class="footer">
            © {{ date('Y') }} Orbit. جميع الحقوق محفوظة.
        </div>
    </div>
</div>
</body>
</html>
