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
}
