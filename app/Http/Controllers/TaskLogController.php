<?php

namespace App\Http\Controllers;

use App\Models\TaskLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TaskLogController extends Controller
{
    protected $table = 'task_log';
    public function getAllTaskLog(): Response
    {
        $taskLogs = DB::table('task_log')->get();
        return response($taskLogs);
    }
}
