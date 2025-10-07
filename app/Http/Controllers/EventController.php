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
    public function index(Request $request)
    {
        $query = Event::query();

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('future')) {
            $query->where('start_time', '>=', now());
        }

        $userId = Auth::id();
        $events = $query->get();

        return Inertia::render('Events/Index', [
            'events' => $events,
            'userId' => $userId,
            'filters' => $request->only(['search', 'future']),
        ]);
    }

    public function myEvents(Request $request)
    {
        $userId = Auth::id();
        $query = Event::where('user_id', $userId);

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('future')) {
            $query->where('start_time', '>=', now());
        }

        $events = $query->get();

        return Inertia::render('Events/MyEvents', [
            'events' => $events,
            'userId' => $userId,
            'filters' => $request->only(['search', 'future']),
        ]);
    }

    public function myInterests(Request $request)
    {
        $userId = Auth::id();

        $query = Event::whereHas('rsvps', function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->whereIn('status', ['going', 'interested']);
        });

        if ($search = $request->input('search')) {
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%");
        });
        }

        if ($request->boolean('future')) {
            $query->where('start_time', '>=', now());
        }

        $events = $query->get();

        return Inertia::render('Events/MyInterests', [
          'events' => $events,
          'userId' => $userId,
          'filters' => $request->only(['search', 'future']),
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
