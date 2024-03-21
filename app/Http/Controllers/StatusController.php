<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller
{
    protected $status = 'status';
    public function getAllStatus(): Response
    {
        $status = DB::table('status')
            ->join('users', 'users.id', '=', 'status.create_by')
            ->select('status.*', 'users.name as user_name', 'users.avatar_url as avatar_url')
            ->orderBy('create_at', 'asc')
            ->get();
        return response($status);
    }

    public function createStatus(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required|string',
            'create_by' => 'required|integer',
        ]);

        try {
            $status = DB::table('status')->insertGetId([
                'name' => $request->input('name'),
                'create_by' => $request->input('create_by'),
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to create status: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'message' => 'Task created successfully!',
            'data' => $status,
        ], 200);
    }

    public function deleteStatus(string $id): JsonResponse
    {
        $tasksCount = DB::table('task')->where('status_id', intval($id))->count();

        if ($tasksCount > 0) {
            return response()->json(['message' => 'Cannot delete status as it is associated with tasks'], 400);
        }
        DB::table('status_setting')
            ->where('next_status', intval($id))
            ->orWhere('current_status', intval($id))
            ->delete();
        DB::table('status')->where('id', intval($id))->delete();
        return response()->json(['message' => 'Status deleted successfully'], 200);
    }
}
