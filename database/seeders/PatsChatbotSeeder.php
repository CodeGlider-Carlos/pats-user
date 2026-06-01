<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PatsChatbotSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PatsIntentsSeeder::class,
            PatsRulesSeeder::class,
            PatsKnowledgeSeeder::class,
            PatsSynonymsSeeder::class,
            PatsOpsKnowledgeSeeder::class,
        ]);
    }
}
