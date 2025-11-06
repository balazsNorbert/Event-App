<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RsvpController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use Inertia\Inertia;

Route::get('/', [EventController::class, 'index'])->name('home');

Route::get('/events', [EventController::class, 'index'])->name('events.index');

Route::get('/events/map', function () {
    return Inertia::render('Events/EventMap');
})->name('events.map');

Route::get('/events/calendar', [EventController::class, 'eventsCalendar'])->name('events.eventsCalendar');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('/events', EventController::class)->except(['index']);
    Route::get('/my-events', [EventController::class, 'myEvents'])->name('events.myEvents');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events/{event}/rsvp', [RsvpController::class, 'store'])->name('events.rsvp');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::delete('/events/{event}/rsvp', [RsvpController::class, 'destroy'])->name('events.rsvp.cancel');
    Route::get('/my-interests', [EventController::class, 'myInterests'])->name('events.myInterests');
    Route::get('/my-events/calendar', [EventController::class, 'myEventsCalendar'])->name('events.myEventsCalendar');
    Route::get('/my-interests/calendar', [EventController::class, 'myInterestsCalendar'])->name('events.myInterestsCalendar');
});

require __DIR__.'/auth.php';
