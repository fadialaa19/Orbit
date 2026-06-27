# TODO: Fix Profile Page Alpine.js Errors

## Status: [x] Complete - Core JS errors fixed

### Steps:
1. [x] **Fix x-data JSON parsing**: Replaced with `@json(array_map(fn($path) => basename($path), ...))` - safe encoding + basenames only.
2. [x] **Update server_required_docs/server_optional_docs**: Handled by basename() in JSON.
3. [ ] **Verify Alpine component parses**: User to test browser console.
4. [ ] **Test tab switching**: Personal, Education, Languages, Documents tabs work.
5. [ ] **Test document upload UI**: File inputs show temp filenames, status updates.
6. [ ] **Test form submission**: Saving state works.
7. [ ] **Optional**: Replace Tailwind CDN with local build.
8. [x] **Complete**: Core fixes done. Test and delete file.

**Current file**: `resources/views/dashboard/profile.blade.php`

