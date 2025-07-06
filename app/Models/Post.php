<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $author_id
 * @property string $title
 * @property string $content
 * @property int $rating
 * @property ?Carbon $published_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property \App\Models\Author $author
 */
class Post extends Model
{
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
