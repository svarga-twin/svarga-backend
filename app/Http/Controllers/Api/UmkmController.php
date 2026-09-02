<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UmkmResource;
use App\Models\Umkm;
use Illuminate\Http\Request;

class UmkmController extends Controller
{
    public function index(Request $request)
    {
        $query = Umkm::query()->active();

        if ($request->filled('koridor_id')) {
            $query->where('koridor_id', $request->query('koridor_id'));
        }

        $perPage = (int) $request->query('per_page', 10);
        $umkms = $query->orderBy('business_name')->paginate($perPage);

        return response()->json([
            'data' => UmkmResource::collection($umkms->items()),
            'meta' => [
                'current_page' => $umkms->currentPage(),
                'last_page' => $umkms->lastPage(),
                'per_page' => $umkms->perPage(),
                'total' => $umkms->total(),
            ],
        ], 200);
    }

    public function show(int $id)
    {
        $umkm = Umkm::query()->active()->find($id);

        if (!$umkm) {
            return response()->json(['message' => 'UMKM tidak ditemukan'], 404);
        }

        return response()->json(['data' => new UmkmResource($umkm)], 200);
    }
}
