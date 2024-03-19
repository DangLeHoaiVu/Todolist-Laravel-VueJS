<?php

namespace App\Http\Controllers;

use App\Models\Status;
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
            ->orderBy('create_at', 'desc')
            ->get();
        return response($status);
    }
}
