<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('statistics:send-daily')
    // ->dailyAt('00:05')
    ->everyMinute()
    ->timezone('Europe/Moscow')
    ->withoutOverlapping();
