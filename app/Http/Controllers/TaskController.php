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

    public function createTask(Request $request): Response
    {
        $task = Task::create($request->all());
        return response($task);
    }

    public function updateTaskStatus(Request $request, int $id): Response
    {
        $this->validate($request, [
            'status_id' => 'required|integer|exists:status,id',
        ]);
        $task = DB::table('task')->where('id', $id)->first();

        if (!$task) {
            return response('Task not found', 404);
        } else {
            DB::table('task')->where('id', $id)->update(['status_id' => $request->get('status_id')]);
            return response('Task updated successfully!', 200);
        }
    }

    public function updateUserAssign(Request $request, int $id): Response
    {
        $this->validate($request, [
            'implementer_id' => 'required|integer|exists:users,id',
        ]);
        $task = DB::table('task')->where('id', $id)->first();

        if (!$task) {
            return response('Task not found', 404);
        } else {
            DB::table('task')->where('id', $id)->update(['implementer_id' => $request->get('implementer_id')]);
            return response('Task updated successfully!', 200);
        }
    }

    public function updateDetailTask(Request $request, int $id): Response
    {
        $this->validate($request, [
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        $task = DB::table('task')->where('id', $id)->first();

        if (!$task) {
            return response('Task not found', 404);
        } else {
            DB::table('task')->where('id', $id)->update(
                [
                    'title' => $request->get('title'),
                    'description' => $request->get('description')
                ]
            );
            return response('Task updated successfully!', 200);
        }
    }
}
