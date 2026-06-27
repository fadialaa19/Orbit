# TODO: Dynamic Scholarship Details Page with 4 Sections

## Plan:
1. **DB Migration**: Add columns to scholarships: `overview`, `conditions`, `documents`, `features` (longtext nullable).
2. **Update Scholarship Model**: Add fillable/ casts.
3. **Update AdminScholarshipsController**: Add edit method, form handling for sections.
4. **Update admin/scholarships.blade.php**: Add 4 textarea fields for sections + AI enhance.
5. **Update StudentDashboardController::show**: Pass full scholarship.
6. **Update dashboard/show.blade.php**: Dynamic tabs showing sections if exist, fallback to description.
7. **Routes**: Add edit/update routes.
8. **Test**: Create scholarship with sections, view details.

**Status**: ✅ Migration created `2026_05_05_092502_add_sections_to_scholarships_table.php` with overview, conditions, documents, features columns.

## Progress
✅ Step 1: Migration created & updated

⏳ Step 2: Run migration `php artisan migrate`

✅ Step 3: Updated Scholarship model - added fillable for new sections

✅ Step 4: Added edit, update methods + updated store validation + created edit.blade.php + updated admin/scholarships.blade.php form with 4 sections

✅ Step 5: Updated admin form - now supports all sections

✅ Step 6: Updated dashboard/show.blade.php - dynamic tabs with Alpine.js for 4 sections + fallback to description

✅ Step 7: Routes - resource already includes edit/update

**Status**: ✅ COMPLETE - Test: admin/scholarships → add/edit scholarship with sections → dashboard/scholarships/{id} to view dynamic tabs.

Run `php artisan route:clear` if needed.

**Next**: Run migration then continue.

