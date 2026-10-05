<?php

namespace Database\Seeders;

use App\Models\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// Menggantikan AdminUser (Prisma/Supabase) yang sebelumnya dipakai login
// svarga-admin. Kredensial demo sama seperti mode mock lama di
// svarga-admin/app/api/auth/login/route.js supaya tidak membingungkan tim:
// admin@svarga.id / svarga123 — GANTI di produksi.
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        UserModel::updateOrCreate(
            ['email' => 'admin@svarga.id'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => Hash::make('svarga123'),
                'is_admin' => true,
            ]
        );
    }
}
