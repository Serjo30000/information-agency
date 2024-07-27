<?php

namespace App\Http\Controllers;

use App\Models\GrandNews;
use Illuminate\Http\Request;

class GrandNewsController extends Controller
{
    public function allGrandNews()
    {
        $grandNews = GrandNews::all();

        return response()->json($grandNews);
    }
}
