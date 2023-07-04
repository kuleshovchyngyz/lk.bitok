<?php

namespace App\Listeners;

use App\Models\Log;
use App\Events\LogActionEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogActionListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\LogActionEvent  $event
     * @return void
     */
    public function handle(LogActionEvent $event)
    {
        Log::create([
            'date' => $event->currentDate,
            'author_id' => $event->authorId,
            'description' => $event->description,
        ]);
    }
}
