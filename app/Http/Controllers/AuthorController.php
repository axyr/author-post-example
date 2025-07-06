<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorFilterRequest;
use App\Http\Resources\AuthorIndexResource;
use App\Models\Author;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{
    public function index(AuthorFilterRequest $request)
    {
        $authors = Author::query()
            ->when($request->validated('filter'), fn($query, $gender) => $query->where('gender', '=', $gender))
            ->when($request->validated('sort'), function ($query, $sort) {
                [$column, $direction] = explode(':', $sort);
                return match ($column) {
                    'rating' => $query->orderBy(
                        DB::raw('(SELECT AVG(posts.rating) FROM posts WHERE posts.author_id = authors.id)'),
                        $direction
                    ),
                    'post_title' => $query->orderBy(
                        DB::raw('(SELECT posts.title FROM posts WHERE posts.author_id = authors.id ORDER BY posts.published_at DESC LIMIT 1)'),
                        $direction
                    ),
                    default => $query->orderBy("authors.$column", $direction),
                };
            })
            ->select([
                'authors.*',
                DB::raw(
                    '(
                    SELECT AVG(posts.rating)
                    FROM posts
                    WHERE posts.author_id = authors.id
                ) as posts_avg_rating'
                ),
                DB::raw(
                    '(
                    SELECT posts.title
                    FROM posts
                    WHERE posts.author_id = authors.id
                    ORDER BY posts.published_at DESC
                    LIMIT 1
                ) as latest_post_title'
                ),
            ])
            ->get();
        return AuthorIndexResource::collection($authors);
    }
}
