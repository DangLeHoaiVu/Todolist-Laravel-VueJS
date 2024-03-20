<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    protected $table = 'task';

    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    public function getAllTask(): Response
    {
        $tasks = DB::table('task')
            ->leftJoin('users as create_user', 'create_user.id', '=', 'task.create_by')
            ->leftJoin('users as implement_user', 'implement_user.id', '=', 'task.implementer_id')
            ->join('status', 'status.id', '=', 'task.status_id')
            ->select(
                'task.*',
                'create_user.id as user_create_id',
                'create_user.name as user_create_name',
                'implement_user.id as user_implement_id',
                'implement_user.name as user_implement_name',
                'implement_user.name as user_implement_avatar_url',
                'status.id as status_id',
                'status.name as status_name'
            )
            ->orderBy('update_at', 'desc')
            ->get();
        return response($tasks);
    }

    public function getTaskDetail(string $id): JsonResponse
    {
        $task = DB::table('task')->where('id', intval($id))->first();
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }
        return response()->json($task);
    }

    public function createTask(Request $request): JsonResponse
    {
        $this->validate($request, [
            'title' => 'required|string',
            'description' => 'required|string',
            'status_id' => 'required|integer|exists:status,id',
            'create_by' => 'required|integer|exists:users,id',
            'start_date' => 'required|integer',
            'end_date' => 'required|integer',
        ]);
        try {
            $task =
                DB::table('task')->insertGetId([
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'status_id' => $request->input('status_id'),
                    'create_by' => $request->input('create_by'),
                    'start_date' => date('Y-m-d H:i:s', $request->input('start_date') / 1000),
                    'end_date' => date('Y-m-d H:i:s', $request->input('end_date') / 1000),
                ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to create task: ' . $e->getMessage()], 500);
        }
        return response()->json([
            'message' => 'Task created successfully!',
            'data' => $task,
        ], 200);
    }

    public function updateTaskStatus(Request $request, int $id): JsonResponse
    {
        $this->validate($request, [
            'status_id' => 'required|integer|exists:status,id',
            'update_at' => 'required|integer'
        ]);
        $task = DB::table('task')->where('id', $id)->first();

        if (!$task) {
            return response('Task not found', 404);
        } else {
            DB::table('task')->where('id', $id)->update([
                'status_id' => $request->get('status_id'),
                'update_at' => date('Y-m-d H:i:s', $request->input('update_at') / 1000),
            ]);
            return response()->json([
                'message' => 'Task update successfully!',
                'data' => $task,
            ], 200);
        }
    }

    public function updateUserAssign(Request $request, int $id): JsonResponse
    {
        $this->validate($request, [
            'implementer_id' => 'required|integer|exists:users,id',
            'update_at' => 'required|integer'
        ]);
        $task = DB::table('task')->where('id', $id)->first();

        if (!$task) {
            return response('Task not found', 404);
        } else {
            DB::table('task')->where('id', $id)->update([
                'implementer_id' => $request->get('implementer_id'),
                'update_at' => date('Y-m-d H:i:s', $request->input('update_at') / 1000),
            ]);
            return response()->json([
                'message' => 'Task update successfully!',
                'data' => $task,
            ], 200);
        }
    }

    public function updateDetailTask(Request $request, int $id): JsonResponse
    {
        $this->validate($request, [
            'title' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|integer',
            'end_date' => 'required|integer',
            'update_at' => 'required|integer'
        ]);

        $task = DB::table('task')->where('id', $id)->first();

        if (!$task) {
            return response('Task not found', 404);
        } else {
            DB::table('task')->where('id', $id)->update(
                [
                    'title' => $request->get('title'),
                    'description' => $request->get('description'),
                    'start_date' => date('Y-m-d H:i:s', $request->input('start_date') / 1000),
                    'end_date' => date('Y-m-d H:i:s', $request->input('end_date') / 1000),
                    'update_at' => date('Y-m-d H:i:s', $request->input('update_at') / 1000),
                ]
            );
            return response()->json([
                'message' => 'Task update successfully!',
                'data' => $task,
            ], 200);
        }
    }
}
