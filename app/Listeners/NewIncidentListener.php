<?php

namespace App\Listeners;

use App\Events\NewIncidentEvent;
use App\Models\IncidentHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewIncidentListener
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
    public function handle(NewIncidentEvent $event): void
    {
        $incident = $event->incident;
        IncidentHistory::create([
            'incident_id' => $incident->id,
            'status_id' => $incident->status_id,
            'user_id' => 1,
        ]);
    }
}
