<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['article_id', 'url'])]
class ArticleView extends Model
{
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
