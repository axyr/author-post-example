<?php

namespace Tests\Unit;

use Database\Factories\AuthorFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_gets_the_full_name(): void
    {
        $author =  AuthorFactory::new()->create([
            'first_name' => 'Martijn',
            'last_name' => 'van Nieuwenhoven',
        ]);

        $this->assertEquals('Martijn van Nieuwenhoven', $author->name);
    }
}
