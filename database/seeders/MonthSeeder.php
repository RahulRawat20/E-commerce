<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $months = [
            ['name'=>'january'],
            ['name'=>'february'],
            ['name'=>'march'],
            ['name'=>'april'],
            ['name'=>'may'],
            ['name'=>'june'],
            ['name'=>'july'],
            ['name'=>'august'],
            ['name'=>'september'],
            ['name'=>'october'],
            ['name'=>'november'],
            ['name'=>'december'],

        ];

        DB::table('month_names')->insert($months);
    }
}
