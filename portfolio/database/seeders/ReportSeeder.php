<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reports = [
            ['photo_id' => 1, 'reporter_id' => 2, 'report_reason' => 'Inappropriate content', 'report_date' => now(), 'status' => 'Pending', 'action_taken' => 'None'],
            ['photo_id' => 2, 'reporter_id' => 1, 'report_reason' => 'Spam', 'report_date' => now(), 'status' => 'Resolved', 'action_taken' => 'Removed'],
        ];

        foreach ($reports as $report) {
            DB::table('reports')->insert($report);
        }
    }
}
