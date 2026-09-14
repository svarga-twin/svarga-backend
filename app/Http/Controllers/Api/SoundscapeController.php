<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SoundscapeResource;
use App\Models\SoundscapeModel;

class SoundscapeController extends Controller
{
    public function show($id)
    {
        $soundscape = SoundscapeModel::findOrFail($id);

        return new SoundscapeResource($soundscape);
    }
}
