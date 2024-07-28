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

    public function findGrandNewsOne($id){
        $grandNewsOne = GrandNews::find($id);

        if ($grandNewsOne){
            return response()->json($grandNewsOne);
        }
        else{
            return response()->json(['message' => 'GrandNewsOne not found'], 404);
        }
    }
}
