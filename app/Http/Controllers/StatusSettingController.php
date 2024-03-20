<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatusSettingController extends Controller
{
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
