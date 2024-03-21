<?php

namespace App\Http\Controllers;

use App\Models\StatusSetting;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class StatusSettingController extends Controller
{
    public function getAllStatusSetting(): Response
    {
        $status_settings = DB::table('status_setting')
            ->join('status as current_status', 'current_status.id', '=', 'status_setting.current_status')
            ->join('status as next_status', 'next_status.id', '=', 'status_setting.next_status')
            ->select(
                'status_setting.*',
                'current_status.name as current_status_name',
                'next_status.name as next_status_name',
            )
            ->get();

        return response($status_settings);
    }
    public function createStatusSetting(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'create_by' => 'required|integer',
                'current_status' => 'required|integer',
                'next_status' => 'required|array',
            ]);

            $createBy = $request->input('create_by');
            $currentStatus = $request->input('current_status');
            $nextStatuses = $request->input('next_status');

            foreach ($nextStatuses as $nextStatus) {
                DB::table('status_setting')->insertGetId([
                    'create_by' => $createBy,
                    'current_status' => $currentStatus,
                    'next_status' => $nextStatus
                ]);
            }
            return response()->json(['message' => 'Status settings saved successfully'], 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to save status settings.'], 500);
        }
    }
}
