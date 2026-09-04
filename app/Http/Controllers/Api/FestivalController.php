<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FestivalResource;
use App\Models\FestivalModel;
use Illuminate\Http\Request;

class FestivalController extends Controller
{
    public function index(Request $request)
    {
        $query = FestivalModel::query()->active();

        if ($request->boolean('upcoming')) {
            $query->upcoming();
        }

        $perPage = (int) $request->query('per_page', 10);
        $festivals = $query->orderBy('event_date')->paginate($perPage);

        return response()->json([
            'data' => FestivalResource::collection($festivals->items()),
            'meta' => [
                'current_page' => $festivals->currentPage(),
                'last_page' => $festivals->lastPage(),
                'per_page' => $festivals->perPage(),
                'total' => $festivals->total(),
            ],
        ], 200);
    }

    public function show(int $id)
    {
        $festival = FestivalModel::query()->active()->find($id);

        if (!$festival) {
            return response()->json(['message' => 'Festival tidak ditemukan'], 404);
        }

        return response()->json(['data' => new FestivalResource($festival)], 200);
    }
}
