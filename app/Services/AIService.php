<?php

// AIService has been replaced by GroqChatService.
// This file is intentionally deleted during migration.
namespace App\Services;

class AIService
{
    public static function ask(string $prompt): string
    {
        return '⚠️ AIService is deprecated. Use GroqChatService instead.';
    }
}

