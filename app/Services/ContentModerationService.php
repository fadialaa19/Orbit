<?php

namespace App\Services;

class ContentModerationService
{
    // قائمة شاملة من الكلمات البذيئة/الشتائم باللهجة الفلسطينية والعربية
    private const OFFENSIVE_KEYWORDS = [
        // شتائم شائعة
        'كلب', 'حيوان', 'قرد', 'خنزير', 'جحش', 'حمار', 'عرص', 'كس', 'طيز',
        'شرموط', 'شرموطه', 'شرموطة', 'قحبه', 'قحبة', 'منيوك', 'منيوك', 'منيك',
        'زب', 'زوب', 'طيزك', 'كسم', 'كس ام', 'كس أخت', 'كس اخت', 'كسختك',
        'قحب', 'منيكه', 'منيكة', 'خول', 'لوطي', 'شاذ', 'عبيط', 'غبي',
        'يلعن', 'يلعنك', 'يلعن ام', 'يلعن أبو', 'يلعن ابو', 'فشخ', 'نيك',
        'انيكك', 'انيك', 'نيكك', 'نيكم', 'كس', 'طيز', 'زبر', 'زب', 'لعنه',
        'كسخت', 'كس عرضك', 'عرص', 'عاهر', 'عاهرة', 'عبيط', 'غبي', 'خرا',
        'زق', 'خره', '/mn9', 'lem3', 'leme3', '7rf',
    ];

    /**
     * Whether the given text contains profanity/abuse.
     */
    public function containsProfanity(string $text): bool
    {
        return $this->containsKeyword($this->normalizeText($text), self::OFFENSIVE_KEYWORDS);
    }

    /**
     * Normalize Arabic text for keyword matching.
     * Removes tashkeel, normalizes alef variants, lowercases.
     */
    public function normalizeText(string $text): string
    {
        $text = mb_strtolower($text);
        // Remove tashkeel/diacritics (must use \u{...} inside a DOUBLE-quoted string -
        // a single-quoted 'ً' is just the literal 6-char string, not a Unicode code point).
        $tashkeel = ["\u{064B}", "\u{064C}", "\u{064D}", "\u{064E}", "\u{064F}", "\u{0650}", "\u{0651}", "\u{0652}"];
        $text = str_replace($tashkeel, '', $text);
        // Normalize alef variants
        $text = str_replace(['أ', 'إ', 'آ', 'ء'], 'ا', $text);
        $text = str_replace(['ة'], 'ه', $text);
        return $text;
    }

    /**
     * Check whether normalized text contains any of the given keywords.
     *
     * Single-word keywords are matched as whole words only (e.g. "كس" must not
     * match inside "باكستان"). Multi-word phrases keep using substring matching
     * since an accidental partial match across word boundaries is effectively
     * impossible. Keywords are normalized the same way as the input text so
     * spelling variants (تاء مربوطة/هاء، همزات) don't need duplicate entries.
     */
    public function containsKeyword(string $normalizedText, array $keywords): bool
    {
        $words = preg_split('/[\s.,!?؟،؛:\-]+/u', $normalizedText, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($keywords as $keyword) {
            $normalizedKeyword = $this->normalizeText($keyword);

            if (str_contains($normalizedKeyword, ' ')) {
                if (str_contains($normalizedText, $normalizedKeyword)) {
                    return true;
                }
            } elseif (in_array($normalizedKeyword, $words, true)) {
                return true;
            }
        }

        return false;
    }
}
