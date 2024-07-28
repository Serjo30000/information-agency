<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function allNews()
    {
        $news = News::all();

        return response()->json($news);
    }

    public function findNewsOne($id){
        $newsOne = News::find($id);

        if ($newsOne){
            return response()->json($newsOne);
        }
        else{
            return response()->json(['message' => 'NewsOne not found'], 404);
        }
    }
}
