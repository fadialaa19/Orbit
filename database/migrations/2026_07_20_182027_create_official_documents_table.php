<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('official_documents', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->default('📄');
            $table->string('title');
            $table->string('source');
            $table->text('description');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // تعبئة القائمة اللي كانت مثبّتة بالكود بشكل مبدئي، حتى الصفحة ما
        // تطلع فاضية أول ما تنترفع هاد الميغريشن على أي بيئة (محلية أو الموقع الحي).
        $now = now();
        \DB::table('official_documents')->insert([
            ['icon' => '🎓', 'title' => 'شهادة الثانوية العامة (التوجيهي)', 'source' => 'وزارة التربية والتعليم', 'description' => 'نسخة رسمية مصدّقة من نتيجة التوجيهي، مطلوبة غالباً لكل طلبات القبول الجامعي بالخارج.', 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['icon' => '🕊️', 'title' => 'شهادة عدم محكومية', 'source' => 'وزارة الداخلية', 'description' => 'شرط أساسي بمعظم طلبات التأشيرة (الفيزا) والقبول بالمنح الدولية.', 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['icon' => '📜', 'title' => 'توثيق وتصديق الشهادات', 'source' => 'وزارة الخارجية', 'description' => 'تصديق الشهادات الدراسية رسمياً حتى تكون معتمدة ومقبولة لدى الجامعات والسفارات الأجنبية.', 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['icon' => '👶', 'title' => 'شهادة الميلاد', 'source' => 'وزارة الداخلية - الأحوال المدنية', 'description' => 'نسخة حديثة مطلوبة عادة ضمن ملف التقديم أو استخراج تأشيرة الدراسة.', 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['icon' => '🛂', 'title' => 'استخراج أو تجديد جواز السفر', 'source' => 'وزارة الداخلية', 'description' => 'نتابع معك إجراءات الاستخراج أو التجديد حتى يكون جاهز قبل موعد سفرك.', 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['icon' => '✅', 'title' => 'شهادة حسن السيرة والسلوك', 'source' => 'المدرسة أو الجامعة', 'description' => 'مستند إضافي تطلبه بعض المنح والجامعات لتقييم الملف الشخصي للطالب.', 'sort_order' => 6, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_documents');
    }
};
