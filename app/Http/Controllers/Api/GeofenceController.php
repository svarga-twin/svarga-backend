<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeofenceResource;
use App\Models\GeofenceModel;

class GeofenceController extends Controller
{
    /** Daftar zona geofencing aktif — dikonsumsi GeofenceContext.jsx sekali saat app dimuat. */
    public function index()
    {
        $zones = GeofenceModel::where('is_active', true)->get();

        return GeofenceResource::collection($zones);
    }
}
