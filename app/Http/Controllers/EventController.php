<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::all();
        $userId = Auth::id();

        return Inertia::render('Events/Index', [
            'events' => $events,
            'userId' => $userId,
        ]);
    }

    public function myEvents()
    {
        $userId = Auth::id();
        $events = Event::where('user_id', $userId)->get();

        return Inertia::render('Events/MyEvents', [
            'events' => $events,
        ]);
    }

    public function myInterests()
    {
        $userId = Auth::id();

        $events = Event::whereHas('rsvps', function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->whereIn('status', ['going', 'interested']);
        })->get();

        return Inertia::render('Events/MyInterests', [
            'events' => $events
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
