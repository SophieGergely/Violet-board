<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Strategy Games',
            'Puzzle',
            'Party Games',
            'Trivia & Knowledge',
            'Card Games',
            'Family Games',
            "Children's Games",
            'Memory Games',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
