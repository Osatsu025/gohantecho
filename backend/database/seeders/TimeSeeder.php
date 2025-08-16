<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $times = ['朝ごはん', '昼ごはん', '夜ごはん', 'おやつ', 'その他'];
        $now = Carbon::now();

        $time_data = [];
        foreach ($times as $time) {
            $time_data[] = [
                'name' => $time,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('times')->insert($time_data);
    }
}
