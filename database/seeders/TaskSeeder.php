<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("task")->insert(
            [
                [
                    "title" => "Demo1",
                    "description" => "Demo1 des",
                    "implementer_id" => null,
                    "create_by" => "2",
                    "status_id" => "1",
                    "start_date" => "2024-03-17",
                    "end_date" => "2024-03-26",
                ],
                [
                    "title" => "Demo2",
                    "description" => "Demo2 des",
                    "implementer_id" => null,
                    "create_by" => "3",
                    "status_id" => "1",
                    "start_date" => "2024-03-18",
                    "end_date" => "2024-03-26",
                ],
            ]
        );
    }
}
