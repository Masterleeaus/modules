<?php

namespace Modules\PMCore\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\PMCore\app\Models\Project;
use Modules\PMCore\app\Models\ProjectChecklist;
use Modules\PMCore\app\Models\ProjectChecklistCompletion;
use Modules\PMCore\app\Models\ProjectChecklistItem;

class JobChecklistController extends Controller
{
    /**
     * List all checklists for a project.
     */
    public function index(Project $project)
    {
        $checklists = $project->checklists()
            ->with(['items.completions' => function ($q) use ($project) {
                $q->where('project_id', $project->id);
            }])
            ->get();

        return view('pmcore::projects.checklist', compact('project', 'checklists'));
    }

    /**
     * Create a new checklist (with items) for a project.
     */
    public function store(Project $project, Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'items'                 => 'required|array|min:1',
            'items.*.description'   => 'required|string|max:500',
            'items.*.is_required'   => 'boolean',
            'items.*.sort_order'    => 'integer|min:0',
        ]);

        $checklist = $project->checklists()->create([
            'name'       => $validated['name'],
            'job_type'   => $project->job_type?->value,
            'is_template' => false,
            'created_by' => Auth::id(),
        ]);

        foreach ($validated['items'] as $index => $item) {
            $checklist->items()->create([
                'description' => $item['description'],
                'sort_order'  => $item['sort_order'] ?? $index,
                'is_required' => $item['is_required'] ?? false,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message'   => __('Checklist created successfully.'),
                'checklist' => $checklist->load('items'),
            ], 201);
        }

        return back()->with('success', __('Checklist created successfully.'));
    }

    /**
     * Mark one or more checklist items as complete.
     */
    public function complete(Project $project, Request $request)
    {
        $validated = $request->validate([
            'item_ids'   => 'required|array|min:1',
            'item_ids.*' => 'integer|exists:project_checklist_items,id',
        ]);

        $completed = [];

        foreach ($validated['item_ids'] as $itemId) {
            $item = ProjectChecklistItem::findOrFail($itemId);

            $completion = ProjectChecklistCompletion::updateOrCreate(
                ['item_id' => $itemId, 'project_id' => $project->id],
                [
                    'checklist_id' => $item->checklist_id,
                    'completed_by' => Auth::id(),
                    'completed_at' => now(),
                ]
            );

            $completed[] = $completion;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message'   => __('Items marked as complete.'),
                'completed' => $completed,
            ]);
        }

        return back()->with('success', __('Checklist items marked as complete.'));
    }

    /**
     * Return all template checklists grouped by job type.
     */
    public function templates()
    {
        $templates = ProjectChecklist::with('items')
            ->where('is_template', true)
            ->get()
            ->groupBy('job_type');

        if (request()->expectsJson()) {
            return response()->json($templates);
        }

        return view('pmcore::projects.checklist-templates', compact('templates'));
    }
}
