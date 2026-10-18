# backend-patch — tambahan svarga-backend untuk dashboard admin

Salin isi folder ini ke `svarga-backend/` (timpa file yang sama), lalu:

```bash
php artisan migrate
php artisan route:list --path=admin     # cek route baru
```

Baru:
- `AdminSensorDeviceController`  — CRUD perangkat sensor + putar kunci API
- `AdminSettingsController`     — pengaturan, info sistem, backup & unduh backup
- `AppSettingModel`, 2 migrasi  — tabel `app_settings`; kolom `name` & `is_maintenance` di `sensor_devices`
- `Console/Commands/MonitorSystem` — `svarga:monitor` (sensor offline & AQI tinggi → notifikasi), terjadwal tiap 5 menit

Diubah (perubahan kecil, ditandai di kode):
- `routes/api.php`, `routes/console.php`
- `AdminNotificationController` — filter `?category=`, `counts`, `filtered_total`
- `MoodLogController@summary` — field `unique_contributors`
- `SensorDeviceModel`, `AdminNotificationModel::notify()`
- `AdminUserController`, `AuthController`, `FestivalController`, `GeofenceController` — mencatat notifikasi admin saat ada data baru
- `PruneSensorReadings` — default hari retensi dari pengaturan `sensor.retention_days`
