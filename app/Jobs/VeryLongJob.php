<?php

namespace App\Jobs;

use App\Models\Article;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Mail\ArticlePosted;
use Illuminate\Http\Request;

class VeryLongJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Article $article, public string $email) {}

    /**
     * Execute the job.
     */
    public function handle(Request $request): void
    {
        Mail::to($this->email)->send(new ArticlePosted($this->article));
    }
}
