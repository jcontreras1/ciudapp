<?php

namespace App\Listeners;

use App\Events\NewTraceIncidentEvent;
use App\Models\IncidentHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewIncidentStatusListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewTraceIncidentEvent $event): void
    {
        $incident = $event->incident;

        IncidentHistory::create([
            'incident_id' => $incident->id,
            'status_id' => $incident->status_id,
            'user_id' => auth()->id(),
            'description' => $event->text,
        ]);
    }
}
