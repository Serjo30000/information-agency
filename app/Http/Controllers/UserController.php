<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\FullName;
use App\Rules\PhoneNumber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function allUsers()
    {
        $users = User::all();

        return response()->json($users, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allUsersListForPanel()
    {
        $users = User::select('id', 'login')->get();

        return response()->json($users, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allUsersPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

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

    public function allUsersBySearchAndSortForPanel(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $sortField = $request->input('sort_field', 'login');
        $sortDirection = $request->input('sort_direction', 'desc');

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $users = User::when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('fio', 'like', "%{$search}%")
                        ->orWhere('login', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortField, $sortDirection)
            ->get();

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

    public function findUserForPanel($id){
        $user = User::select('id', 'login')->find($id);

        if ($user){
            return response()->json($user, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'User not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function deleteUserMark(Request $request){
        $rules = [
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $usersItems = User::whereIn('id', $request->input('user_ids'))->get();

        foreach ($usersItems as $item) {
            $item->delete_mark = !$item->delete_mark;
            $item->save();
        }

        $usersItems = User::whereIn('id', $request->input('user_ids'))
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'User delete_mark updated successfully',
            'user' => $usersItems,
        ], 200, [], JSON_UNESCAPED_UNICODE);
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

        if (!$user->delete_mark){
            return response()->json([
                'success' => false,
                'message' => 'Cannot be deleted'
            ], 403, [], JSON_UNESCAPED_UNICODE);
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

    public function editUserForFields($id, Request $request){
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
                    'message' => 'Cannot edit user with admin or super_admin role'
                ], 403, [], JSON_UNESCAPED_UNICODE);
            }
        }

        $rules = [
            'login' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'login')->ignore($user->id),
            ],
            'password' => 'required|string|min:8|confirmed',
            'fio' => ['required','string','max:255',new FullName()],
            'phone' => ['required', 'string', 'max:15', Rule::unique('users', 'phone')->ignore($user->id), new PhoneNumber],
            'sys_Comment' => 'nullable|string',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        if ($user->tokens()->count()>0){
            $user->tokens()->delete();
        }

        $user->login = $request->input('login');
        $user->password = $request->input('password');
        $user->fio = $request->input('fio');
        $user->phone = $request->input('phone');
        $user->sys_Comment = $request->input('sys_Comment');

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user' => $user,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findRoleForUser($id){
        $user = User::find($id);

        if ($user == null){
            return response()->json(['message' => 'User not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json($user->getRoleNames()->toArray(), 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function editUserForRole($id, Request $request){
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $rules = [
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        if ($user->tokens()->count()>0){
            $user->tokens()->delete();
        }

        $currentRoles = $user->getRoleNames()->toArray();

        $requestedRoles = $request->input('roles');

        $rolesToRemove = array_diff($currentRoles, $requestedRoles);

        $rolesToAdd = array_diff($requestedRoles, $currentRoles);

        foreach ($rolesToRemove as $role) {
            $user->removeRole($role);
        }

        foreach ($rolesToAdd as $role) {
            $user->assignRole($role);
        }

        return response()->json([
            'success' => true,
            'message' => 'Roles updated successfully',
            'roles' => $user->getRoleNames(),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
