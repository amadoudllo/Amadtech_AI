<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class AdminSetting extends Model
{
    protected $table = 'admin_settings';
    
    protected $fillable = [
        'setting_key',
        'setting_value',
        'type',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function getValue($key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        if ($setting->type === 'json') {
            return json_decode($setting->setting_value, true);
        } elseif ($setting->type === 'boolean') {
            return filter_var($setting->setting_value, FILTER_VALIDATE_BOOLEAN);
        } elseif ($setting->type === 'integer') {
            return (int)$setting->setting_value;
        }

        return $setting->setting_value;
    }

    public static function setValue($key, $value, $type = 'string', $description = null)
    {
        if ($type === 'json' && is_array($value)) {
            $value = json_encode($value);
        }

        return self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'type' => $type,
                'description' => $description,
            ]
        );
    }
}
