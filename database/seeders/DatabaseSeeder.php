<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Post;
use App\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => Role::Admin,
        ]);

        $categories = Category::factory(5)->create();

        $tags = Tag::factory(10)->create();

        Post::factory(30)
            ->recycle($categories)
            ->create()
            ->each(fn (Post $post) => $post->tags()->attach(
                $tags->random(rand(1, 3))->pluck('id')
            ));
    }
}
