# TODO - Scholarships Rich Editor (Quill) + Main Image

- [ ] Create migration: add `main_image` to `scholarships` table
- [ ] Update `app/Models/Scholarship.php` fillable + casts if needed
- [ ] Add route + controller method to handle Quill image upload (store file, return URL)
- [ ] Update create form (`resources/views/admin/scholarships.blade.php`):
  - [ ] set `enctype="multipart/form-data"`
  - [ ] add file input for `main_image`
  - [ ] replace each textarea section with Quill editor + hidden input storing HTML
  - [ ] make AI generation fill hidden inputs/editor contents
- [ ] Update edit form (`resources/views/admin/scholarships/edit.blade.php`):
  - [ ] set `enctype="multipart/form-data"` and add file input + preview
  - [ ] replace each textarea with Quill editor + hidden inputs
- [ ] Update `AdminScholarshipController@store` and `@update` to handle `main_image` upload and persist Quill HTML fields
- [ ] Update public display pages (if they exist) to render HTML safely (use `{!! !!}` or purifier if needed)
- [ ] Run `php artisan storage:link`
- [ ] Run `php artisan migrate`
- [ ] Remove/ignore no-op migration placeholder `2026_05_24_000001_quill_scholarship_rich_editor_images_table.php` (not used)

- [ ] Manual QA: create/edit scholarship, upload main image, insert images inside Quill content

