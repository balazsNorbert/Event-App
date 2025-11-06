<?php

namespace App\Http\Controllers;

use App\Models\Rsvp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RsvpController extends Controller
{
    public function store(Request $request, $eventId)
    {
        $request->validate([
            'status' => 'required|in:going,interested,not_going'
        ]);

        Rsvp::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'event_id' => $eventId
            ],
            [
                'status' => $request->status
            ]
        );

        return back()->with('success', 'Your RSVP has been saved.');
    }

    public function destroy($eventId)
    {
        Rsvp::where('user_id', Auth::id())
            ->where('event_id', $eventId)
            ->delete();

        return back()->with('success', 'Your RSVP has been removed.');
    }
}

