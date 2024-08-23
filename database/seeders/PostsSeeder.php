<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post; 

class PostsSeeder extends Seeder
{
    public function run(): void
    {
        Post::factory()->count(10)->create();
    }
}
