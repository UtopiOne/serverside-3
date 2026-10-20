<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'published_at',
    'title',
    'preview_image',
    'full_image',
    'brief_description',
    'description'
])]
class Article extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['published_at' => 'date'];
    }
}
