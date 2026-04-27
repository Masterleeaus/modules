<?php

namespace Modules\TitanIntegrations\Http\Controllers\Api\V1;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BookingApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $companyId = $request->attributes->get('api_company_id');

        $query = Task::where('company_id', $companyId)
            ->where('task_type', 'booking');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('from')) {
            $query->whereDate('start_date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('start_date', '<=', $request->input('to'));
        }

        $bookings = $query->with(['project', 'users'])->paginate(50);

        return response()->json($bookings);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $companyId = $request->attributes->get('api_company_id');
        $booking   = Task::where('company_id', $companyId)
            ->where('task_type', 'booking')
            ->findOrFail($id);

        return response()->json($booking->load(['project', 'users']));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'heading'    => 'required|string|max:255',
            'start_date' => 'required|date',
            'due_date'   => 'nullable|date|after_or_equal:start_date',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $companyId = $request->attributes->get('api_company_id');

        $task = Task::create(array_merge($request->only([
            'heading', 'description', 'start_date', 'due_date', 'project_id',
            'status', 'priority',
        ]), [
            'company_id' => $companyId,
            'task_type'  => 'booking',
        ]));

        return response()->json($task, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $companyId = $request->attributes->get('api_company_id');
        $task      = Task::where('company_id', $companyId)->findOrFail($id);

        $task->update($request->only([
            'heading', 'description', 'start_date', 'due_date', 'status', 'priority',
        ]));

        return response()->json($task->fresh());
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $companyId = $request->attributes->get('api_company_id');
        Task::where('company_id', $companyId)->findOrFail($id)->delete();

        return response()->json(['ok' => true]);
    }
}
