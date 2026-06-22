<?php

namespace Database\Seeders;

use App\Models\Label;
use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    public function run(): void
    {
        $labels = [
            'New',
            'Bestseller',
        ];

        foreach ($labels as $name) {
            Label::firstOrCreate(['name' => $name]);
        }
    }
}
