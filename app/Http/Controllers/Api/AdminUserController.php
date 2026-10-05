<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminUserResource;
use App\Models\MoodLogModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Endpoint khusus dashboard admin (svarga-admin) — menggantikan
 * userService.js + dashboardService.js sisi Prisma/Supabase. Semua route
 * di sini di-guard middleware ['auth:sanctum', 'admin'] (lihat routes/api.php).
 */
class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = max(1, min(100, (int) $request->query('per_page', 5)));

        $query = UserModel::query()->orderByDesc('created_at');

        // Pencarian nama/email + filter status — dikerjakan di sisi server
        // supaya hasilnya benar lintas halaman (bukan cuma halaman yang tampil).
        if ($request->filled('q')) {
            $term = '%' . $request->query('q') . '%';
            $query->where(fn ($w) => $w->where('name', 'like', $term)->orWhere('email', 'like', $term));
        }
        if ($request->query('status') === 'aktif') {
            $query->where('is_active', true);
        } elseif ($request->query('status') === 'nonaktif') {
            $query->where('is_active', false);
        }

        $users = $query->paginate($perPage);

        return response()->json([
            'data' => AdminUserResource::collection($users->items())->resolve(),
            'page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'per_page' => $users->perPage(),
            'filtered_total' => $users->total(),
            'total' => UserModel::count(),
            'aktif' => UserModel::where('is_active', true)->count(),
            'nonaktif' => UserModel::where('is_active', false)->count(),
            'baru_7_hari' => UserModel::where('created_at', '>=', now()->subDays(7))->count(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'is_admin' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Data pengguna tidak valid', 'errors' => $validator->errors()], 422);
        }

        // Berbeda dari register publik: di sini ADMIN yang membuat akun, jadi
        // is_admin boleh diset (endpoint ini di-guard middleware 'admin').
        $user = UserModel::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'is_admin' => $request->boolean('is_admin', false),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json(['message' => 'Pengguna berhasil dibuat', 'data' => new AdminUserResource($user)], 201);
    }

    public function update(Request $request, $id)
    {
        $user = UserModel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'is_admin' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Data pengguna tidak valid', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Cegah admin mencabut hak admin / menonaktifkan DIRINYA SENDIRI —
        // kalau tidak, satu klik salah bisa mengunci semua orang dari dashboard.
        if ($request->user('sanctum')->id === $user->id) {
            if (array_key_exists('is_admin', $data) && !$data['is_admin']) {
                return response()->json(['message' => 'Tidak bisa mencabut hak admin akun sendiri.'], 422);
            }
            if (array_key_exists('is_active', $data) && !$data['is_active']) {
                return response()->json(['message' => 'Tidak bisa menonaktifkan akun sendiri.'], 422);
            }
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json(['message' => 'Pengguna berhasil diperbarui', 'data' => new AdminUserResource($user)]);
    }

    public function destroy(Request $request, $id)
    {
        $user = UserModel::findOrFail($id);

        if ($request->user('sanctum')->id === $user->id) {
            return response()->json(['message' => 'Tidak bisa menghapus akun sendiri.'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'Pengguna berhasil dihapus']);
    }

    /**
     * Tren bulanan pendaftaran pengguna vs catatan mood, 5 bulan terakhir —
     * dipakai grafik "Aktivitas Pengguna vs Catatan Mood" di halaman
     * Laporan & Analitik admin.
     */
    public function activityMonthly(Request $request)
    {
        $months = (int) ($request->query('months') ?? 5);
        $bulanLabel = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();

            $result[] = [
                'bulan' => $bulanLabel[$monthStart->month - 1],
                'aktif' => UserModel::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'catatan_mood' => MoodLogModel::whereBetween('logged_at', [$monthStart, $monthEnd])->count(),
            ];
        }

        return response()->json(['data' => $result]);
    }
}
