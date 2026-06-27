<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimonial;
use App\Models\User;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'أحمد محمد',
                'university' => 'جامعة تورنتو - كندا',
                'content' => 'قبلت بمنحة كاملة التمويل في أقل من شهرين! الدعم كان مذهلاً والفريق ساعدني في كل خطوة من خطوات التقديم.',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&q=80',
            ],
            [
                'name' => 'فاطمة علي',
                'university' => 'جامعة برلين الحرة - ألمانيا',
                'content' => 'مطابقة الـ AI كانت دقيقة 97%. لم أكن أتخيل الأمر بهذه السهولة، وجدت 12 منحة مناسبة لشروطي في أسبوع واحد.',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=400&q=80',
            ],
            [
                'name' => 'عمر خالد',
                'university' => 'جامعة ملبورن - أستراليا',
                'content' => 'مراجعة الوثائق غيّرت كل شيء. حصلت على القبول الأسبوع الماضي بعدما عدلت خطاب التحفيز بناءً على ملاحظات الاستشاري.',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f03?w=400&q=80',
            ],
            [
                'name' => 'سارة عبدالله',
                'university' => 'جامعة مانشستر - بريطانيا',
                'content' => 'خدمة الاستشارات الشخصية كانت استثماراً رائعاً. حصلت على قبول مباشر في تخصص الذكاء الاصطناعي مع منحة جزئية.',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&q=80',
            ],
            [
                'name' => 'محمد سالم',
                'university' => 'جامعة طوكيو - اليابان',
                'content' => 'كنت متردداً في التقديم على الجامعات اليابانية لكن الفريق شرح لي كل التفاصيل بالعربي. الحمدلله حصلت على القبول!',
                'rating' => 4,
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&q=80',
            ],
            [
                'name' => 'نورة الفهد',
                'university' => 'جامعة هارفارد - أمريكا',
                'content' => 'المنح الحصرية اللي توفرت لي كمشتركة VIP كانت السبب الرئيسي في قبولي. شكراً بدون قيود على دعمكم المستمر.',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400&q=80',
            ],
            [
                'name' => 'خالد الرشيد',
                'university' => 'جامعة أمستردام - هولندا',
                'content' => 'التنبيهات بالمواعيد النهائية أنقذتني أكثر من مرة. النظام يرسل إشعارات قبل أسبوع من كل موعد مهم. خدمة رائعة جداً.',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&q=80',
            ],
            [
                'name' => 'ليلى حسن',
                'university' => 'جامعة السوربون - فرنسا',
                'content' => 'ساعدوني في ترجمة الوثائق وتصديقها بطريقة احترافية. القبول في السوربون كان حلمي والآن أعيشه.',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&q=80',
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

