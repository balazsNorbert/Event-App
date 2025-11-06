<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Event;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $events = Event::with(['rsvps' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
        ->orderBy('start_time', 'asc')
        ->take(3)
        ->get();

        $events->transform(function ($event) {
            $event->user_status = optional($event->rsvps->first())->status;
            unset($event->rsvps);
            return $event;
        });

        return Inertia::render('Dashboard', [
            'user' => Auth::user(),
            'events' => $events,
        ]);
    }
}
