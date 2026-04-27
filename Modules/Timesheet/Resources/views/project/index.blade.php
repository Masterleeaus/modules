@php
    $timesheets = Modules\Timesheet\Entities\Timesheet::where('project_id', $project->id)
        ->where('type', 'project')
        ->get();
@endphp
<div class="table-responsive">
    <table class="table mb-0 ">
        <thead>
            <tr>
                <th>{{ __('User') }}</th>
                @if (module_is_active('Taskly', $project->created_by))
                    <th>{{ __('Project') }}</th>
                    <th>{{ __('Task') }}</th>
                @endif
                <th>{{ __('Date') }}</th>
                <th>{{ __('Hours') }}</th>
                <th>{{ __('minutes') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($timesheets as $timesheet)
                <tr>
                    <td>{{ $timesheet->user->name }}</td>
                    @if (module_is_active('Taskly', $project->created_by))
                        <td>{{ !empty($timesheet->project->name) ? $timesheet->project->name : '-' }}</td>
                        <td>{{ !empty($timesheet->task->title) ? $timesheet->task->title : '-' }}</td>
                    @endif
                    <td>{{ company_date_formate($timesheet->date, $project->created_by, $project->workspace) }}</td>
                    <td>{{ $timesheet->hours }}</td>
                    <td>{{ $timesheet->minutes }}</td>
                </tr>
            @empty
                @include('layouts.nodatafound')
            @endforelse
        </tbody>
    </table>
</div>
