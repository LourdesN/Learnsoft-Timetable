<?php

use Illuminate\Database\Seeder;
use App\Models\Breaks;

class BreaksTableSeeder extends Seeder
{
    public function run()
    {
        $breaksData = [
            ['name' => 'Morning Break', 'duration_minutes' => 15],
            ['name' => 'Tea Break', 'duration_minutes' => 30],
            ['name' => 'Lunch Break', 'duration_minutes' => 60],
            // Add more break data as needed
        ];

        foreach ($breaksData as $data) {
            Breaks::create($data);
        }
    }
}

