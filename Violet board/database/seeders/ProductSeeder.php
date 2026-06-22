<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('data/games.json');

        if (!file_exists($jsonPath)) {
            $this->command->error('Dataset not found!');
            $this->command->line('');
            $this->command->line('Place the dataset at: database/data/games.json');
            $this->command->line('You can find it in the repository under database/data/');
            return;
        }

        $games      = json_decode(file_get_contents($jsonPath), true);
        $categories = Category::all()->keyBy('name');

        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Run CategorySeeder first.');
            return;
        }

        $created = 0;
        $skipped = 0;

        foreach ($games as $game) {
            if (Product::where('name', $game['name'])->exists()) {
                $skipped++;
                continue;
            }

            $product = Product::create([
                'name'          => $game['name'],
                'description'   => $game['description'] ?? null,
                'price'         => $this->generatePrice($game['weight'] ?? null),
                'min_age'       => $game['min_age'] ?? 0,
                'min_players'   => $game['min_players'] ?? 1,
                'max_players'   => max($game['min_players'] ?? 1, $game['max_players'] ?? 1),
                'play_time_min' => $game['play_time_min'] ?? null,
                'play_time_max' => $game['play_time_max'] ?? null,
                'bgg_rating'    => isset($game['bgg_rating']) && $game['bgg_rating'] >= 1
                    ? round($game['bgg_rating'], 2)
                    : null,
                'weight'        => isset($game['weight']) && $game['weight'] >= 1
                    ? round($game['weight'], 2)
                    : null,
                'in_stock'      => true,
            ]);

            $categoryIds = [];
            foreach ($game['categories'] ?? [] as $categoryName) {
                if ($categories->has($categoryName)) {
                    $categoryIds[] = $categories->get($categoryName)->id;
                }
            }

            if (!empty($categoryIds)) {
                $product->categories()->attach(array_unique($categoryIds));
            }

            $this->command->info("  ✓ {$game['name']}");
            $created++;
        }

        $this->command->line('');
        $this->command->info("Done! Created: {$created}, Skipped (already exists): {$skipped}");
    }

    /**
     * Generates a realistic price based on game complexity.
     * weight 1 → ~€16, weight 5 → ~€75
     */
    private function generatePrice(?float $weight): float
    {
        $base  = $weight ? ($weight * 12) + 10 : 29.99;
        $price = $base + rand(-3, 6);

        return round(max(9.99, min(89.99, $price)), 2);
    }
}
