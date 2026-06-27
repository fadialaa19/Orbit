ملاحظة إعدادات Google OAuth

يجب ضبط ملف .env:
- GOOGLE_CLIENT_ID
- GOOGLE_CLIENT_SECRET
- APP_URL

ثم في Google Cloud Console أضف Redirect URI التالي:
- {APP_URL}/auth/google/callback

مثال:
- http://localhost/auth/google/callback

