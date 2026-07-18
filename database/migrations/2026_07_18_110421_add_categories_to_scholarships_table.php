<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            // مصفوفة المراحل الدراسية - يسمح بمنحة تشمل أكثر من برنامج (مثلاً
            // بكالوريوس وماجستير معاً). عمود category الفردي يبقى موجود
            // كأول عنصر منها للتوافق مع أي كود قديم ما زال يقرأه مباشرة.
            $table->json('categories')->nullable()->after('category');
        });

        // ترحيل البيانات الحالية: كل منحة قديمة تصير مصفوفة من عنصر واحد
        DB::table('scholarships')->whereNotNull('category')->get(['id', 'category'])->each(function ($row) {
            DB::table('scholarships')->where('id', $row->id)->update([
                'categories' => json_encode([$row->category]),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->dropColumn('categories');
        });
    }
};
