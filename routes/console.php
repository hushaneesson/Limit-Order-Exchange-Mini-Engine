<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:match-open-orders')->everyMinute()
    ->withoutOverlapping();
