<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'logo_path',
        'favicon_path',
        'primary_color',
        'payment_gateways',
        'ai_api_key',
        'maintenance_mode',
        'maintenance_message',
        'contact_email',
        'contact_phone',
        'facebook_url',
        'instagram_url',
        'whatsapp_url',
        'telegram_url',
        'document_service_enabled',
    ];

    protected $casts = [
        'payment_gateways' => 'array',
        'maintenance_mode' => 'boolean',
        'document_service_enabled' => 'boolean',
    ];

    public static function get($key, $default = null)
    {
        return Cache::remember('setting_' . $key, 3600, function () use ($key, $default) {
            return self::first()?->$key ?? $default;
        });
    }

    public static function set($key, $value)
    {
        Cache::forget('setting_' . $key);
        self::updateOrCreate(['id' => 1], [$key => $value]);
    }
}

