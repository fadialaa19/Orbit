<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimonial;
use App\Models\User;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        // Safe to re-run on every deploy: skip if testimonials already exist
        // (avoids duplicate rows since db:seed runs on every container boot).
        if (Testimonial::count() > 0) {
            return;
        }

        $testimonials = [
            [
                'name' => 'أحمد محمد',
                'university' => 'جامعة تورنتو - كندا',
                'content' => 'قبلت بمنحة كاملة التمويل في أقل من شهرين! الدعم كان مذهلاً والفريق ساعدني في كل خطوة من خطوات التقديم.',
                'rating' => 5,
            ],
            [
                'name' => 'فاطمة علي',
                'university' => 'جامعة برلين الحرة - ألمانيا',
                'content' => 'مطابقة الـ AI كانت دقيقة. لم أكن أتخيل الأمر بهذه السهولة، وجدت منح مناسبة لشروطي خلال أسبوع واحد فقط.',
                'rating' => 5,
            ],
            [
                'name' => 'عمر خالد',
                'university' => 'جامعة ملبورن - أستراليا',
                'content' => 'مراجعة الوثائق غيّرت كل شيء. حصلت على القبول بعدما عدلت خطاب التحفيز بناءً على ملاحظات الاستشاري.',
                'rating' => 5,
            ],
            [
                'name' => 'سارة عبدالله',
                'university' => 'جامعة مانشستر - بريطانيا',
                'content' => 'خدمة الاستشارات الشخصية كانت استثماراً رائعاً. حصلت على قبول مباشر في تخصص الذكاء الاصطناعي مع منحة جزئية.',
                'rating' => 5,
            ],
            [
                'name' => 'محمد سالم',
                'university' => 'جامعة طوكيو - اليابان',
                'content' => 'كنت متردداً في التقديم على الجامعات اليابانية لكن الفريق شرح لي كل التفاصيل بالعربي. الحمدلله حصلت على القبول!',
                'rating' => 4,
            ],
        ];

        // Try to link some testimonials to real students
        $studentIds = User::students()->pluck('id')->toArray();

        foreach ($testimonials as $index => $data) {
            if (!empty($studentIds) && $index < count($studentIds)) {
                $data['user_id'] = $studentIds[$index];
            }
            Testimonial::create($data);
        }
    }
}
