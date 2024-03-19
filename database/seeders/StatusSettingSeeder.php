<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("status_setting")->insert(
            [
                [
                    "create_by" => "1",
                    "current_status" => "1",
                    "next_status" => "2",
                ],
                [
                    "create_by" => "1",
                    "current_status" => "1",
                    "next_status" => "4",
                ],
                [
                    "create_by" => "1",
                    "current_status" => "2",
                    "next_status" => "3",
                ],
                [
                    "create_by" => "1",
                    "current_status" => "2",
                    "next_status" => "4",
                ],
                [
                    "create_by" => "1",
                    "current_status" => "3",
                    "next_status" => "4",
                ],
                [
                    "create_by" => "1",
                    "current_status" => "3",
                    "next_status" => "1",
                ],
            ]
        );
    }
}
