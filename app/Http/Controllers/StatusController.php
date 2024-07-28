<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function allStatuses()
    {
        $statuses = Status::all();

        return response()->json($statuses);
    }

    public function findStatus($id){
        $status = Status::find($id);

        if ($status){
            return response()->json($status);
        }
        else{
            return response()->json(['message' => 'Status not found'], 404);
        }
    }
}
