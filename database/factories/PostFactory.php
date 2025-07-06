<?php
namespace Database\Factories;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'author_id' => fn() => AuthorFactory::new(),
            'title' => fake()->title(),
            'content' => fake()->text(),
            'rating' => fake()->randomElement([1,2,3,4,5,6,7,8,9,10]),
            'published_at' => Carbon::now(),
        ];
    }
}
