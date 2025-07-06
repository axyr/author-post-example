<?php

namespace Tests\Feature;

use App\Enums\Gender;
use Carbon\Carbon;
use Database\Factories\AuthorFactory;
use Database\Factories\PostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_empty_list_of_authors(): void
    {
        $response = $this->get('/authors');

        $response->assertStatus(200);
    }

    public function test_it_returns_a_single_author_without_posts(): void
    {
        AuthorFactory::new()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => Gender::Female,
        ]);

        $response = $this->get('/authors');

        $response->assertStatus(200);

        $response->assertJsonPath('data', function ($data) {
            $this->assertCount(1, $data);
            $this->assertEquals('Jane Doe', $data[0]['name']);
            $this->assertEquals('f', $data[0]['gender']);
            $this->assertEquals(null, $data[0]['posts_avg_rating']);
            $this->assertEquals(null, $data[0]['latest_post_title']);
            return true;
        });
    }

    public function test_it_returns_a_single_author_with_one_post(): void
    {
        $author = AuthorFactory::new()->create();
        PostFactory::new()->for($author)->create(['rating' => 5, 'title' => 'My first post']);

        $response = $this->get('/authors');

        $response->assertStatus(200);

        $response->assertJsonPath('data', function ($data) {
            $this->assertEquals(5, $data[0]['posts_avg_rating']);
            $this->assertEquals('My first post', $data[0]['latest_post_title']);
            return true;
        });
    }

    public function test_it_returns_a_single_author_with_many_posts_average(): void
    {
        $author = AuthorFactory::new()->create();
        PostFactory::new()->for($author)->create(['rating' => 3, 'title' => 'My first post', 'published_at' => Carbon::now()->subDays(3)]);
        PostFactory::new()->for($author)->create(['rating' => 4, 'title' => 'My second post', 'published_at' => Carbon::now()->subDays(2)]);
        PostFactory::new()->for($author)->create(['rating' => 5, 'title' => 'My last post', 'published_at' => Carbon::now()->subDays(1)]);

        $response = $this->get('/authors');

        $response->assertStatus(200);

        $response->assertJsonPath('data', function ($data) {
            $this->assertEquals(4, $data[0]['posts_avg_rating']);
            $this->assertEquals('My last post', $data[0]['latest_post_title']);
            return true;
        });
    }

    public function test_it_returns_many_authors_with_many_posts_average(): void
    {
        $author = AuthorFactory::new()->create();
        PostFactory::new()->for($author)->create(['rating' => 3, 'title' => 'My first A post', 'published_at' => Carbon::now()->subDays(3)]);
        PostFactory::new()->for($author)->create(['rating' => 4, 'title' => 'My second A post', 'published_at' => Carbon::now()->subDays(2)]);
        PostFactory::new()->for($author)->create(['rating' => 5, 'title' => 'My last A post', 'published_at' => Carbon::now()->subDays(1)]);

        $author = AuthorFactory::new()->create();
        PostFactory::new()->for($author)->create(['rating' => 6, 'title' => 'My first B post', 'published_at' => Carbon::now()->subDays(3)]);
        PostFactory::new()->for($author)->create(['rating' => 7, 'title' => 'My second B post', 'published_at' => Carbon::now()->subDays(2)]);
        PostFactory::new()->for($author)->create(['rating' => 8, 'title' => 'My last B post', 'published_at' => Carbon::now()->subDays(1)]);

        $response = $this->get('/authors');

        $response->assertStatus(200);

        $response->assertJsonPath('data', function ($data) {
            $this->assertCount(2, $data);
            $this->assertEquals(4, $data[0]['posts_avg_rating']);
            $this->assertEquals('My last A post', $data[0]['latest_post_title']);
            $this->assertEquals(7, $data[1]['posts_avg_rating']);
            $this->assertEquals('My last B post', $data[1]['latest_post_title']);
            return true;
        });
    }

    public function test_it_filters_authors_by_gender(): void
    {
        AuthorFactory::new()->create(['gender' => Gender::Male]);
        AuthorFactory::new()->create(['gender' => Gender::Female]);

        $response = $this->get('/authors?filter=f');

        $response->assertJsonPath('data', function ($data) {
            $this->assertCount(1, $data);
            $this->assertEquals('f', $data[0]['gender']);
            return true;
        });

        $response = $this->get('/authors?filter=m');

        $response->assertJsonPath('data', function ($data) {
            $this->assertCount(1, $data);
            $this->assertEquals('m', $data[0]['gender']);
            return true;
        });
    }

    public function test_it_sorts_by_first_name(): void
    {
        AuthorFactory::new()->create(['first_name' => 'A', 'last_name' => '']);
        AuthorFactory::new()->create(['first_name' => 'B', 'last_name' => '']);
        AuthorFactory::new()->create(['first_name' => 'C', 'last_name' => '']);

        $response = $this->get('/authors?sort=first_name:asc');
        $this->assertEquals(['A', 'B', 'C'], $response->json('data.*.name'));

        $response = $this->get('/authors?sort=first_name:desc');
        $this->assertEquals(['C', 'B', 'A'], $response->json('data.*.name'));
    }

    public function test_it_sorts_by_rating(): void
    {
        $author = AuthorFactory::new()->create();
        PostFactory::new()->for($author)->create(['rating' => 3, 'title' => 'My first A post', 'published_at' => Carbon::now()->subDays(3)]);
        PostFactory::new()->for($author)->create(['rating' => 4, 'title' => 'My second A post', 'published_at' => Carbon::now()->subDays(2)]);
        PostFactory::new()->for($author)->create(['rating' => 5, 'title' => 'My last A post', 'published_at' => Carbon::now()->subDays(1)]);

        $author = AuthorFactory::new()->create();
        PostFactory::new()->for($author)->create(['rating' => 6, 'title' => 'My first B post', 'published_at' => Carbon::now()->subDays(3)]);
        PostFactory::new()->for($author)->create(['rating' => 7, 'title' => 'My second B post', 'published_at' => Carbon::now()->subDays(2)]);
        PostFactory::new()->for($author)->create(['rating' => 8, 'title' => 'My last B post', 'published_at' => Carbon::now()->subDays(1)]);

        $response = $this->get('/authors?sort=rating:asc');
        $this->assertEquals([4, 7], $response->json('data.*.posts_avg_rating'));

        $response = $this->get('/authors?sort=rating:desc');
        $this->assertEquals([7, 4], $response->json('data.*.posts_avg_rating'));
    }

    public function test_it_sorts_by_post_title(): void
    {
        $author = AuthorFactory::new()->create();
        PostFactory::new()->for($author)->create(['rating' => 3, 'title' => 'My first A post', 'published_at' => Carbon::now()->subDays(3)]);
        PostFactory::new()->for($author)->create(['rating' => 4, 'title' => 'My second A post', 'published_at' => Carbon::now()->subDays(2)]);
        PostFactory::new()->for($author)->create(['rating' => 5, 'title' => 'My last A post', 'published_at' => Carbon::now()->subDays(1)]);

        $author = AuthorFactory::new()->create();
        PostFactory::new()->for($author)->create(['rating' => 6, 'title' => 'My first B post', 'published_at' => Carbon::now()->subDays(3)]);
        PostFactory::new()->for($author)->create(['rating' => 7, 'title' => 'My second B post', 'published_at' => Carbon::now()->subDays(2)]);
        PostFactory::new()->for($author)->create(['rating' => 8, 'title' => 'My last B post', 'published_at' => Carbon::now()->subDays(1)]);

        $response = $this->get('/authors?sort=post_title:asc');
        $this->assertEquals(['My last A post', 'My last B post'], $response->json('data.*.latest_post_title'));

        $response = $this->get('/authors?sort=post_title:desc');
        $this->assertEquals(['My last B post', 'My last A post'], $response->json('data.*.latest_post_title'));
    }
}
