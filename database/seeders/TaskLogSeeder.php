<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("task_log")->insert(
            [
                [
                    "task_id" => "1",
                    "implementer_id" => null,
                    "status_id" => "1",
                    "create_by" => "2",
                    "log" => "Created",
                    "comment" => null,
                    "type" => "CREATED",
                ],
                [
                    "task_id" => "1",
                    "implementer_id" => "2",
                    "status_id" => "1",
                    "create_by" => "2",
                    "log" => "Assign for user1",
                    "comment" => null,
                    "type" => "ASSIGN",
                ],
                [
                    "task_id" => "1",
                    "implementer_id" => "2",
                    "status_id" => "2",
                    "create_by" => "2",
                    "log" => "Change status",
                    "comment" => null,
                    "type" => "CHANGE_STATUS",
                ],
            ]
        );
    }
}
