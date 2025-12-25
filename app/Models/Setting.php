<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = Cache::remember("setting.{$key}", 3600, function () use ($key) {
            return static::where('key', $key)->first();
        });

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("setting.{$key}");
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup(string $group): array
    {
        $settings = Cache::remember("settings.group.{$group}", 3600, function () use ($group) {
            return static::where('group', $group)->get();
        });

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }

        return $result;
    }

    /**
     * Clear settings cache for a group
     */
    public static function clearGroupCache(string $group): void
    {
        $settings = static::where('group', $group)->get();
        
        foreach ($settings as $setting) {
            Cache::forget("setting.{$setting->key}");
        }
        
        Cache::forget("settings.group.{$group}");
    }

    /**
     * Get bank account settings
     */
    public static function getBankAccount(): array
    {
        return static::getByGroup('bank_account');
    }
}
