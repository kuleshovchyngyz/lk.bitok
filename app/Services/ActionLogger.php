<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Events\LogActionEvent;

class ActionLogger
{
    public static function log($description)
    {
        $today = Carbon::today();
        $author = Auth::user()->id;
        event(new LogActionEvent($today, $author, $description));
    }
}
