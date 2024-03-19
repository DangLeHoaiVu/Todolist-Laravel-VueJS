<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("status")->insert(
            [
                [
                    "name" => "Open",
                    "create_by" => "1",
                ],
                [
                    "name" => "Processing",
                    "create_by" => "1",
                ],
                [
                    "name" => "Done",
                    "create_by" => "1",
                ],
                [
                    "name" => "Close",
                    "create_by" => "1",
                ]
            ]
        );
    }
}
