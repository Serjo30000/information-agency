<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class StatusController extends Controller
{
    public function allStatuses()
    {
        $statuses = Status::all();

        return response()->json($statuses, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allStatusesPaginate(Request $request)
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

        $statuses = Status::all();

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $statuses = $statuses->filter(function($status) use ($startDate) {
                return Carbon::parse($status->created_at)->greaterThanOrEqualTo($startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $statuses = $statuses->filter(function($status) use ($endDate) {
                return Carbon::parse($status->created_at)->lessThanOrEqualTo($endDate);
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $statuses->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $statuses->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findStatus($id){
        $status = Status::find($id);

        if ($status){
            return response()->json($status, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'Status not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
