<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\Storage;
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

    public function eventsCalendar(Request $request)
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

        $events = $query->get(['id','title','start_time','end_time','location']);

        return Inertia::render('Events/Calendar', [
            'events' => $events,
            'filters' => $request->only(['search', 'future']),
        ]);
    }

    public function myEventsCalendar(Request $request)
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

        $userEvents = $query->get(['id','title','start_time','end_time','location']);

        return Inertia::render('Events/MyEventsCalendar', [
            'events' => $userEvents,
            'filters' => $request->only(['search', 'future']),
        ]);
    }

    public function myInterestsCalendar(Request $request)
    {
        $userId = Auth::id();
        $query = Event::whereHas('rsvps', function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->whereIn('status', ['going','interested']);
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

        $userInterests = $query->get(['id','title','start_time','end_time','location']);

        return Inertia::render('Events/MyInterestsCalendar', [
            'events' => $userInterests,
            'filters' => $request->only(['search', 'future']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Events/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'start_time' => 'required|date|after_or_equal:now',
            'end_time' => 'required|date|after_or_equal:start_time',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        $path = $request->hasFile('image') ? $request->file('image')->store('event-images', 'public') : null;

        Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'user_id' => $userId,
            'image' => $path,
        ]);

        return redirect()->route('events.index')
                         ->with('success', 'Event created successfully!');
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
    public function edit(Event $event)
    {
        $userId = Auth::id();

        if ($event->user_id !== $userId) {
          abort(403);
        }

        return Inertia::render('Events/Edit', [
            'event' => $event,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $userId = Auth::id();

        if ($event->user_id !== $userId) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'start_time' => 'required|date|after_or_equal:now',
            'end_time' => 'required|date|after_or_equal:start_time',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        $data = $request->only(['title','description','location','start_time','end_time']);

        if ($request->hasFile('image')) {
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image')->store('event-images', 'public');
        }

        $event->update($data);

        return redirect()->route('events.index')
                        ->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $userId = Auth::id();

        if ($event->user_id !== $userId) {
        abort(403);
        }

        $event->delete();

        return redirect()->route('events.index')
                        ->with('success', 'Event deleted successfully!');
    }
}
