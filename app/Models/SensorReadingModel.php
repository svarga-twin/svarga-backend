<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorReadingModel extends Model
{
    use HasFactory;

    protected $table = 'sensor_readings';

    public const TYPE_TEMPERATURE = 'temperature';
    public const TYPE_AIR_QUALITY = 'air_quality';

    public const TYPE_PM10 = 'pm10';
    public const TYPE_SO2 = 'so2';
    public const TYPE_CO = 'co';
    public const TYPE_O3 = 'o3';
    public const TYPE_NO2 = 'no2';

    public const POLLUTANT_TYPES = [
        self::TYPE_PM10,
        self::TYPE_SO2,
        self::TYPE_CO,
        self::TYPE_O3,
        self::TYPE_NO2,
    ];

    public const TYPES = [
        self::TYPE_TEMPERATURE,
        self::TYPE_AIR_QUALITY,
        ...self::POLLUTANT_TYPES,
    ];

    protected $fillable = [
        'device_code',
        'koridor_id',
        'sensor_type',
        'value',
        'unit',
        'recorded_at',
    ];

    protected $casts = [
        'value' => 'float',
        'recorded_at' => 'datetime',
    ];

    public function scopeType($query, string $sensorType)
    {
        return $query->where('sensor_type', $sensorType);
    }

    public function scopeKoridor($query, ?string $koridorId)
    {
        return $koridorId ? $query->where('koridor_id', $koridorId) : $query;
    }

    /** Unit bawaan per jenis sensor, dipakai kalau ESP32 tidak mengirim field `unit`. */
    public static function defaultUnitFor(string $sensorType): ?string
    {
        return match ($sensorType) {
            self::TYPE_TEMPERATURE => '°C',
            self::TYPE_AIR_QUALITY => 'AQI',
            self::TYPE_PM10,
            self::TYPE_SO2,
            self::TYPE_CO,
            self::TYPE_O3,
            self::TYPE_NO2 => 'µg/m³',
            default => null,
        };
    }
}
