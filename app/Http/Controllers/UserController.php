<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    public function allUsers()
    {
        $users = User::all();

        return response()->json($users, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allUsersPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $users = User::all();

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $users = $users->filter(function($user) use ($startDate) {
                return Carbon::parse($user->created_at)->greaterThanOrEqualTo($startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $users = $users->filter(function($user) use ($endDate) {
                return Carbon::parse($user->created_at)->lessThanOrEqualTo($endDate);
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $users->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $users->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findUser($id){
        $user = User::find($id);

        if ($user){
            return response()->json($user, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'User not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
