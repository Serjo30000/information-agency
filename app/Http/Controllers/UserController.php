<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

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

    public function deleteUser($id){
        $userAuth = Auth::guard('sanctum')->user();

        if (!$userAuth) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($userAuth->hasRole('admin') && !$userAuth->hasRole('super_admin')){
            if ($user->hasAnyRoles(['admin', 'super_admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete user with admin or super_admin role'
                ], 403, [], JSON_UNESCAPED_UNICODE);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else if ($userAuth->hasRole('super_admin')){
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete user'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
