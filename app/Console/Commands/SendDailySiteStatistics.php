<?php

namespace App\Console\Commands;

use App\Mail\DailySiteStatistics;
use App\Models\ArticleView;
use App\Models\Comment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

#[Signature('statistics:send-daily')]
#[Description('Send yesterday site statistics to moderators')]
class SendDailySiteStatistics extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $timezone = config('app.timezone');
        $reportDay = CarbonImmutable::now($timezone)->subDay()->startOfDay();
        $nextDay = $reportDay->addDay();

        $articleViews = ArticleView::query()
            ->where('created_at', '>=', $reportDay)
            ->where('created_at', '<', $nextDay)
            ->count();

        $newComments = Comment::query()
            ->where('created_at', '>=', $reportDay)
            ->where('created_at', '<', $nextDay)
            ->count();

        $moderators = User::query()
            ->whereHas('role', function (Builder $query): void {
                $query->where('name', 'moderator');
            })
            ->get();

        foreach ($moderators as $moderator) {
            Mail::to($moderator->email)->send(
                new DailySiteStatistics($reportDay, $articleViews, $newComments)
            );
        }

        $this->info("Отчёт отправлен модераторам: {$moderators->count()}");

        return self::SUCCESS;
    }
}
