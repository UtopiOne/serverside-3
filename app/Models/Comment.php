<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

#[Fillable([
    'published_at',
    'contents',
])]
class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        $invalidateComments = function (Comment $comment): void {
            Cache::put("article:{$comment->article_id}:comments:version", (string) Str::uuid(), 120);
        };

        static::saved($invalidateComments);
        static::deleted($invalidateComments);
    }

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'approved' => 'boolean'];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
