<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Author
 */
class AuthorIndexResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'gender' => $this->gender->value,
            'posts_avg_rating' => $this->posts_avg_rating,
            'latest_post_title' => $this->latest_post_title,
        ];
    }
}
