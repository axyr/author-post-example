<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
{
    protected $model = Author::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->firstName(),
            'gender' => fake()->randomElement([Gender::Male, Gender::Female]),
        ];
    }
}
