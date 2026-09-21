<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    public const DEFAULT_APPLICATION_NAME = 'Eka Janma, Eka Taru';

    public const DEFAULT_APPLICATION_TAGLINE = 'Gerakan penanaman pohon Provinsi Bali';

    protected $fillable = ['key', 'value'];

    protected $hidden = ['value'];

    protected function casts(): array
    {
        return ['value' => 'encrypted'];
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return static::query()->where('key', $key)->first()?->value ?? $default;
    }

    /**
     * @return array{applicationName: string, applicationTagline: string}
     */
    public static function branding(): array
    {
        return [
            'applicationName' => static::getValue('application_name', static::DEFAULT_APPLICATION_NAME),
            'applicationTagline' => static::getValue('application_tagline', static::DEFAULT_APPLICATION_TAGLINE),
        ];
    }
}
