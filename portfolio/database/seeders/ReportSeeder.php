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
            ['photo_id' => 1, 'reporter_id' => 2, 'violator_id'=> 1, 'report_reason' => 'Inappropriate content', 'report_date' => now(), 'status' => 'pending', 'action_taken' => 'none'],
            ['photo_id' => 2, 'reporter_id' => 1,'violator_id'=> 2, 'report_reason' => 'Spam', 'report_date' => now(), 'status' => 'resolved', 'action_taken' => 'removed'],
        ];

        foreach ($reports as $report) {
            DB::table('reports')->insert($report);
        }
    }
}
