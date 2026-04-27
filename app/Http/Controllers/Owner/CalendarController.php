<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\JobCalendarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class CalendarController extends Controller
{
    public function index(Request $request): Response|ResponseFactory
    {
        return inertia('Owner/Calendar');
    }

    /**
     * Returns jobs as FullCalendar-compatible events for a given date range.
     */
    public function events(Request $request, JobCalendarService $calendarService): JsonResponse
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end'   => ['required', 'date'],
        ]);

        $events = $calendarService->getEvents(
            $request->user()->organization_id,
            $request->start,
            $request->end,
        );

        return response()->json($events);
    }
}
