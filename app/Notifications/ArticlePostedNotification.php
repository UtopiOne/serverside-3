<?php

namespace App\Notifications;

use App\Models\Article;
use Illuminate\Notifications\Notification;

class ArticlePostedNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(public Article $article) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id' => $this->article->id,
            'article_title' => $this->article->title,
        ];
    }
}
