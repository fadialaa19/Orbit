# TODO_SCHOLARSHIPS_HTML_QUILL_EDITPLAN

## 1) التحقق من البنية الحالية
- [x] التأكد أن الأقسام في `resources/views/admin/scholarships.blade.php` و `resources/views/admin/scholarships/edit.blade.php` عبارة عن `textarea` حاليا.
- [x] التأكد أن `ScholarshipRichTextUploadController` موجود ويرفع صور Quill (حقل `image` -> يرجع `url`).
- [x] التأكد أن جدول `scholarships` يحتوي عمود `main_image`.

## 2) Edit Plan النهائي (اختيار حفظ HTML)
- [ ] تحديد أسماء hidden inputs التي سترسل HTML Quill لكل حقل.
  - overview -> `overview_html`
  - conditions -> `conditions_html`
  - documents -> `documents_html`
  - features -> `features_html`
  - description (إن كانت موجودة في edit أيضا؛ في create فقط حاليا) -> `description_html`

- [ ] تعديل Blade:
  - [ ] استبدال `textarea` بحقل Quill لكل قسم.
  - [ ] إضافة hidden inputs لكل `_html` مع قيم ابتدائية من DB (عند وجوده في edit).
  - [ ] إضافة `input type="file" name="main_image"` في create و edit (مع عرض صورة حالية في edit إن وجدت).

- [ ] تعديل `AdminScholarshipController.php`:
  - [ ] إضافة validate لحقل `main_image` (nullable|image|max:...)
  - [ ] تعديل store/update لاستقبال `*_html` بدل `*` النصية الحالية.
  - [ ] استبدال حفظ البيانات بحيث يتم:
    - overview = request('overview_html')
    - conditions = request('conditions_html')
    - documents = request('documents_html')
    - features = request('features_html')
    - description = request('description_html') (في store فقط إن كانت موجودة)
  - [ ] معالجة رفع `main_image` وتعيين المسار/الرابط في عمود `main_image`.

## 3) اختبارات يدوية بعد التنفيذ
- [ ] إنشاء scholarship جديد وإضافة محتوى Quill + صورة main_image، ثم تأكيد حفظ HTML في DB.
- [ ] تعديل scholarship موجود: التأكد أن المحتوى السابق يعرض بشكل صحيح في Quill وأن الـ HTML لا يتحول لنص.
- [ ] اختبار رفع صور داخل Quill والتأكد من ظهورها في المحتوى المحفوظ.

