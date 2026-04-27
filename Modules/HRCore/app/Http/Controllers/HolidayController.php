<?php

namespace Modules\HRCore\app\Http\Controllers;

use App\ApiClasses\Error;
use App\ApiClasses\Success;
use App\Helpers\FormattingHelper;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\HRCore\app\Models\Holiday;
use Yajra\DataTables\DataTables;

class HolidayController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrcore.view-holidays', ['only' => ['index', 'datatable', 'show', 'indexAjax', 'getByIdAjax']]);
        $this->middleware('permission:hrcore.create-holidays', ['only' => ['create', 'store', 'addOrUpdateHolidayAjax']]);
        $this->middleware('permission:hrcore.edit-holidays', ['only' => ['edit', 'update', 'toggleStatus', 'changeStatusAjax', 'addOrUpdateHolidayAjax']]);
        $this->middleware('permission:hrcore.delete-holidays', ['only' => ['destroy', 'deleteAjax']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hrcore::holidays.index');
    }

    /**
     * Display holidays for employees (My Holidays view)
     */
    public function myHolidays()
    {
        $currentYear = date('Y');
        $user = auth()->user();

        // Get holidays applicable to current user
        $holidays = Holiday::active()
            ->visibleToEmployees()
            ->whereYear('date', $currentYear)
            ->orderBy('date')
            ->get()
            ->filter(function ($holiday) use ($user) {
                return $holiday->isApplicableFor($user);
            });

        // Group holidays by month
        $holidaysByMonth = $holidays->groupBy(function ($holiday) {
            return $holiday->date->format('F');
        });

        // Get upcoming holidays
        $upcomingHolidays = Holiday::active()
            ->visibleToEmployees()
            ->upcoming()
            ->limit(5)
            ->get()
            ->filter(function ($holiday) use ($user) {
                return $holiday->isApplicableFor($user);
            });

        // Get holiday count by type
        $totalHolidays = $holidays->count();
        $pastHolidays = $holidays->where('date', '<', now())->count();
        $futureHolidays = $holidays->where('date', '>=', now())->count();

        return view('hrcore::holidays.my-holidays', compact(
            'holidays',
            'holidaysByMonth',
            'upcomingHolidays',
            'totalHolidays',
            'pastHolidays',
            'futureHolidays',
            'currentYear'
        ));
    }

    /**
     * Get data for DataTable via AJAX
     */
    public function datatable(Request $request)
    {
        $holidays = Holiday::query();

        // Apply year filter if provided
        if ($request->has('year') && $request->year) {
            $holidays->where('year', $request->year);
        }

        // Apply type filter if provided
        if ($request->has('type') && $request->type) {
            $holidays->where('type', $request->type);
        }

        return DataTables::of($holidays)
            ->addColumn('date_formatted', function ($holiday) {
                return FormattingHelper::formatDate($holiday->date).' ('.$holiday->day.')';
            })
            ->addColumn('type_badge', function ($holiday) {
                $colors = [
                    'public' => 'primary',
                    'religious' => 'info',
                    'regional' => 'warning',
                    'optional' => 'secondary',
                    'company' => 'success',
                    'special' => 'danger',
                ];
                $color = $colors[$holiday->type] ?? 'secondary';

                return '<span class="badge bg-'.$color.'">'.ucfirst($holiday->type).'</span>';
            })
            ->addColumn('applicability', function ($holiday) {
                if ($holiday->applicable_for === 'all') {
                    return '<span class="badge bg-success">All Employees</span>';
                }

                $badge = '<span class="badge bg-info">'.ucfirst(str_replace('_', ' ', $holiday->applicable_for)).'</span>';

                if ($holiday->applicable_for === 'department' && $holiday->departments) {
                    $count = count($holiday->departments);
                    $badge .= ' <small class="text-muted">('.$count.' dept'.($count > 1 ? 's' : '').')</small>';
                }

                return $badge;
            })
            ->addColumn('tags', function ($holiday) {
                $tags = '';

                if ($holiday->is_optional) {
                    $tags .= '<span class="badge bg-label-secondary me-1">Optional</span>';
                }
                if ($holiday->is_restricted) {
                    $tags .= '<span class="badge bg-label-warning me-1">Restricted</span>';
                }
                if ($holiday->is_half_day) {
                    $tags .= '<span class="badge bg-label-info me-1">Half Day</span>';
                }
                if ($holiday->is_compensatory) {
                    $tags .= '<span class="badge bg-label-primary me-1">Compensatory</span>';
                }
                if ($holiday->is_recurring) {
                    $tags .= '<span class="badge bg-label-success me-1">Recurring</span>';
                }

                return $tags;
            })
            ->addColumn('status_badge', function ($holiday) {
                if ($holiday->is_active) {
                    return '<span class="badge bg-success">Active</span>';
                } else {
                    return '<span class="badge bg-secondary">Inactive</span>';
                }
            })
            ->addColumn('actions', function ($holiday) {
                $actions = [];

                if (auth()->user()->can('hrcore.edit-holidays')) {
                    $actions[] = [
                        'label' => __('Edit'),
                        'icon' => 'bx bx-edit',
                        'onclick' => "editHoliday({$holiday->id})",
                    ];

                    $actions[] = [
                        'label' => $holiday->is_active ? __('Deactivate') : __('Activate'),
                        'icon' => $holiday->is_active ? 'bx bx-x-circle' : 'bx bx-check-circle',
                        'onclick' => "toggleStatus({$holiday->id})",
                    ];
                }

                if (auth()->user()->can('hrcore.delete-holidays')) {
                    if (! empty($actions)) {
                        $actions[] = ['divider' => true];
                    }
                    $actions[] = [
                        'label' => __('Delete'),
                        'icon' => 'bx bx-trash',
                        'onclick' => "deleteHoliday({$holiday->id})",
                    ];
                }

                return view('components.datatable-actions', [
                    'id' => $holiday->id,
                    'actions' => $actions,
                ])->render();
            })
            ->rawColumns(['type_badge', 'applicability', 'tags', 'status_badge', 'actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hrcore::holidays.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:50|unique:holidays,code',
            'date' => 'required|date',
            'type' => 'required|in:public,religious,regional,optional,company,special',
            'category' => 'nullable|in:national,state,cultural,festival,company_event,other',
            'description' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
            'is_optional' => 'boolean',
            'is_restricted' => 'boolean',
            'is_recurring' => 'boolean',
            'is_half_day' => 'boolean',
            'half_day_type' => 'nullable|required_if:is_half_day,1|in:morning,afternoon',
            'half_day_start_time' => 'nullable|required_if:is_half_day,1',
            'half_day_end_time' => 'nullable|required_if:is_half_day,1',
            'is_compensatory' => 'boolean',
            'compensatory_date' => 'nullable|required_if:is_compensatory,1|date',
            'applicable_for' => 'required|in:all,department,location,employee_type,custom',
            'departments' => 'nullable|array',
            'locations' => 'nullable|array',
            'employee_types' => 'nullable|array',
            'specific_employees' => 'nullable|array',
            'color' => 'nullable|string|max:7',
            'is_visible_to_employees' => 'boolean',
            'send_notification' => 'boolean',
            'notification_days_before' => 'nullable|integer|min:0|max:30',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return Error::response($validator->errors()->first());
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Prepare data
            $data = $request->all();

            // Convert checkbox values
            $data['is_optional'] = $request->has('is_optional');
            $data['is_restricted'] = $request->has('is_restricted');
            $data['is_recurring'] = $request->has('is_recurring');
            $data['is_half_day'] = $request->has('is_half_day');
            $data['is_compensatory'] = $request->has('is_compensatory');
            $data['is_visible_to_employees'] = $request->has('is_visible_to_employees');
            $data['send_notification'] = $request->has('send_notification');
            $data['is_active'] = true;

            // Set year and day from date
            $date = Carbon::parse($data['date']);
            $data['year'] = $date->year;
            $data['day'] = $date->format('l');

            $holiday = Holiday::create($data);

            DB::commit();

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return Success::response(__('Holiday created successfully!'));
            }

            // For regular form submission, redirect with success message
            return redirect()->route('hrcore.holidays.index')
                ->with('success', __('Holiday created successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create holiday: '.$e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return Error::response(__('Failed to create holiday. Please try again.'));
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to create holiday. Please try again.'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $holiday = Holiday::findOrFail($id);

            return Success::response([
                'id' => $holiday->id,
                'name' => $holiday->name,
                'code' => $holiday->code,
                'date' => $holiday->date->format('Y-m-d'),
                'date_formatted' => FormattingHelper::formatDate($holiday->date),
                'type' => $holiday->type,
                'category' => $holiday->category,
                'description' => $holiday->description,
                'notes' => $holiday->notes,
                'color' => $holiday->color,
                'is_optional' => $holiday->is_optional,
                'is_restricted' => $holiday->is_restricted,
                'is_recurring' => $holiday->is_recurring,
                'is_half_day' => $holiday->is_half_day,
                'half_day_type' => $holiday->half_day_type,
                'half_day_start_time' => $holiday->half_day_start_time,
                'half_day_end_time' => $holiday->half_day_end_time,
                'is_compensatory' => $holiday->is_compensatory,
                'compensatory_date' => $holiday->compensatory_date?->format('Y-m-d'),
                'applicable_for' => $holiday->applicable_for,
                'departments' => $holiday->departments,
                'locations' => $holiday->locations,
                'employee_types' => $holiday->employee_types,
                'is_visible_to_employees' => $holiday->is_visible_to_employees,
                'send_notification' => $holiday->send_notification,
                'notification_days_before' => $holiday->notification_days_before,
                'is_active' => $holiday->is_active,
            ]);
        } catch (\Exception $e) {
            return Error::response(__('Holiday not found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $holiday = Holiday::findOrFail($id);

        return view('hrcore::holidays.edit', compact('holiday'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:50|unique:holidays,code,'.$id,
            'date' => 'required|date',
            'type' => 'required|in:public,religious,regional,optional,company,special',
            'category' => 'nullable|in:national,state,cultural,festival,company_event,other',
            'description' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
            'is_optional' => 'boolean',
            'is_restricted' => 'boolean',
            'is_recurring' => 'boolean',
            'is_half_day' => 'boolean',
            'half_day_type' => 'nullable|required_if:is_half_day,1|in:morning,afternoon',
            'half_day_start_time' => 'nullable|required_if:is_half_day,1',
            'half_day_end_time' => 'nullable|required_if:is_half_day,1',
            'is_compensatory' => 'boolean',
            'compensatory_date' => 'nullable|required_if:is_compensatory,1|date',
            'applicable_for' => 'required|in:all,department,location,employee_type,custom',
            'departments' => 'nullable|array',
            'locations' => 'nullable|array',
            'employee_types' => 'nullable|array',
            'specific_employees' => 'nullable|array',
            'color' => 'nullable|string|max:7',
            'is_visible_to_employees' => 'boolean',
            'send_notification' => 'boolean',
            'notification_days_before' => 'nullable|integer|min:0|max:30',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return Error::response($validator->errors()->first());
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $holiday = Holiday::findOrFail($id);

            DB::beginTransaction();

            // Prepare data
            $data = $request->all();

            // Convert checkbox values
            $data['is_optional'] = $request->has('is_optional');
            $data['is_restricted'] = $request->has('is_restricted');
            $data['is_recurring'] = $request->has('is_recurring');
            $data['is_half_day'] = $request->has('is_half_day');
            $data['is_compensatory'] = $request->has('is_compensatory');
            $data['is_visible_to_employees'] = $request->has('is_visible_to_employees');
            $data['send_notification'] = $request->has('send_notification');

            // Set year and day from date
            $date = Carbon::parse($data['date']);
            $data['year'] = $date->year;
            $data['day'] = $date->format('l');

            $holiday->update($data);

            DB::commit();

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return Success::response(__('Holiday updated successfully!'));
            }

            // For regular form submission, redirect with success message
            return redirect()->route('hrcore.holidays.index')
                ->with('success', __('Holiday updated successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update holiday: '.$e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return Error::response(__('Failed to update holiday. Please try again.'));
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to update holiday. Please try again.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $holiday = Holiday::findOrFail($id);

            DB::beginTransaction();
            $holiday->delete();
            DB::commit();

            return Success::response(__('Holiday deleted successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete holiday: '.$e->getMessage());

            return Error::response(__('Failed to delete holiday. Please try again.'));
        }
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus($id)
    {
        try {
            $holiday = Holiday::findOrFail($id);
            $holiday->is_active = ! $holiday->is_active;
            $holiday->save();

            return Success::response(__('Holiday status updated successfully!'));
        } catch (\Exception $e) {
            Log::error('Failed to update holiday status: '.$e->getMessage());

            return Error::response(__('Failed to update holiday status. Please try again.'));
        }
    }

    // Legacy methods for backward compatibility
    public function indexAjax(Request $request)
    {
        return $this->datatable($request);
    }

    public function addOrUpdateHolidayAjax(Request $request)
    {
        if ($request->id) {
            return $this->update($request, $request->id);
        } else {
            return $this->store($request);
        }
    }

    public function getByIdAjax($id)
    {
        return $this->show($id);
    }

    public function deleteAjax($id)
    {
        return $this->destroy($id);
    }

    public function changeStatusAjax($id)
    {
        return $this->toggleStatus($id);
    }
}
