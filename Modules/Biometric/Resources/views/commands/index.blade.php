@extends('layouts.app')

@push('styles')
    <style>
        .badge {
            display: inline;
        }
    </style>
@endpush

@section('filter-section')
    <x-filters.filter-box>
        <!-- SEARCH BY TASK START -->
        <div class="task-search d-flex py-1 pr-lg-3 px-0 border-right-grey align-items-center">
            <form class="w-100 mr-1 mr-lg-0 mr-md-1 ml-md-1 ml-0 ml-lg-0">
                <div class="input-group bg-grey rounded">
                    <div class="input-group-prepend">
                        <span class="input-group-text border-0 bg-additional-grey">
                            <i class="fa fa-search f-13 text-dark-grey"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control f-14 p-1 border-additional-grey" id="search-text-field"
                        placeholder="@lang('app.startTyping')">
                </div>
            </form>
        </div>
        <!-- SEARCH BY TASK END -->

        <!-- FILTER BY STATUS START -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey">
            <div class="select-status">
                <select class="form-control select-picker" name="status" id="status-filter">
                    <option value="all">@lang('app.all')</option>
                    <option value="pending">@lang('app.pending')</option>
                    <option value="sent">@lang('biometric::app.sent')</option>
                    <option value="executed">@lang('biometric::app.executed')</option>
                    <option value="failed">@lang('app.failed')</option>
                </select>
            </div>
        </div>
        <!-- FILTER BY STATUS END -->

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->
    </x-filters.filter-box>
@endsection

@section('content')
    <div class="content-wrapper">
        <!-- Status Definitions -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="fa fa-info-circle text-info mr-2"></i>
                            @lang('biometric::app.commandStatusDefinitions')
                        </h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="mr-3">
                                        <span class="badge badge-warning p-2">
                                            <i class="fa fa-clock mr-1"></i>
                                            @lang('biometric::app.pending')
                                        </span>
                                    </div>
                                    <div class="text-muted">
                                        @lang('biometric::app.pendingDefinition')
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="mr-3">
                                        <span class="badge badge-info p-2">
                                            <i class="fa fa-paper-plane mr-1"></i>
                                            @lang('biometric::app.sent')
                                        </span>
                                    </div>
                                    <div class="text-muted">
                                        @lang('biometric::app.sentDefinition')
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="mr-3">
                                        <span class="badge badge-success p-2">
                                            <i class="fa fa-check-circle mr-1"></i>
                                            @lang('biometric::app.executed')
                                        </span>
                                    </div>
                                    <div class="text-muted">
                                        @lang('biometric::app.executedDefinition')
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="mr-3">
                                        <span class="badge badge-danger p-2">
                                            <i class="fa fa-times-circle mr-1"></i>
                                            @lang('biometric::app.failed')
                                        </span>
                                    </div>
                                    <div class="text-muted">
                                        @lang('biometric::app.failedDefinition')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
            <x-table class="table-hover">
                <x-slot name="thead">
                    <th>#</th>
                    <th>@lang('app.employee')</th>
                    <th>@lang('biometric::app.deviceName')</th>
                    <th>@lang('biometric::app.commandType')</th>
                    <th>@lang('app.status')</th>
                    <th>@lang('app.createdAt')</th>
                    <th>@lang('biometric::app.sentAt')</th>
                    <th>@lang('biometric::app.executedAt')</th>
                    <th>@lang('biometric::app.failedAt')</th>
                </x-slot>

                @forelse($pendingCommands as $key => $command)
                    <tr id="row-{{ $command->id }}" class="command-row"
                        data-command-type="{{ $command->type }}"
                        data-status="{{ $command->status }}">
                        <td>{{ $key + 1 }}</td>
                        <td>
                            @if($command->user)
                                <div class="d-flex align-items-center">
                                    <span class="px-2 py-1 rounded mr-2">
                                        <span id="employee-id-{{ $command->employee_id }}">{{  !is_null($command->employee_id) ? $command->employee_id : '-' }}</span>
                                    </span>
                                        <x-employee :user="$command->user" />
                                </div>
                            @else
                                --
                            @endif
                        </td>
                        <td>
                            @if($command->device)
                                {{ $command->device->device_name }}

                            @else
                                --
                            @endif
                        </td>
                        <td>
                            @if($command->type == 'DELETEUSER')
                                <span class="badge badge-danger">
                                    {{ $command->type }}
                                </span>
                            @elseif($command->type == 'CREATEUSER')
                                <span class="badge badge-info">
                                    {{ $command->type }}
                                </span>
                            @else
                                {{ $command->type }}
                            @endif
                        </td>
                        <td>
                            @if($command->status == 'pending')
                                <span class="badge badge-warning">
                                    <i class="fa fa-clock mr-1"></i>
                                    @lang('biometric::app.pending')
                                </span>
                            @elseif($command->status == 'sent')
                                <span class="badge badge-info">
                                    <i class="fa fa-paper-plane mr-1"></i>
                                    @lang('biometric::app.sent')
                                </span>
                            @elseif($command->status == 'executed')
                                <span class="badge badge-success">
                                    <i class="fa fa-check-circle mr-1"></i>
                                    @lang('biometric::app.executed')
                                </span>
                            @elseif($command->status == 'failed')
                                <span class="badge badge-danger">
                                    <i class="fa fa-times-circle mr-1"></i>
                                    @lang('biometric::app.failed')
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="font-weight-bold">
                                {{ $command->created_at->setTimezone(company()->timezone)->format(company()->time_format) }}
                            </div>
                            <div class="text-muted small">
                                @if($command->created_at->isToday())
                                    @lang('app.today')
                                @else
                                    {{ $command->created_at->setTimezone(company()->timezone)->format(company()->date_format) }}
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            @if($command->sent_at)
                                <div class="font-weight-bold">
                                    {{ $command->sent_at->setTimezone(company()->timezone)->format(company()->time_format) }}
                                </div>
                                <div class="text-muted small">
                                    @if($command->sent_at->isToday())
                                        @lang('app.today')
                                    @else
                                        {{ $command->sent_at->setTimezone(company()->timezone)->format(company()->date_format) }}
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($command->executed_at)
                                <div class="font-weight-bold">
                                    {{ $command->executed_at->setTimezone(company()->timezone)->format(company()->time_format) }}
                                </div>
                                <div class="text-muted small">
                                    @if($command->executed_at->isToday())
                                        @lang('app.today')
                                    @else
                                        {{ $command->executed_at->setTimezone(company()->timezone)->format(company()->date_format) }}
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($command->failed_at)
                                <div class="font-weight-bold">
                                    {{ $command->failed_at->setTimezone(company()->timezone)->format(company()->time_format) }}
                                </div>
                                <div class="text-muted small">
                                    @if($command->failed_at->isToday())
                                        @lang('app.today')
                                    @else
                                        {{ $command->failed_at->setTimezone(company()->timezone)->format(company()->date_format) }}
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="shadow-none">
                            <x-cards.no-record icon="list" :message="__('messages.noRecordFound')" />
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Filter functionality
            $('#status-filter').change(function() {
                const status = $(this).val();
                const searchTerm = $('#search-text-field').val().toLowerCase();

                filterCommands(status, searchTerm);
            });

            // Reset filters
            $('#reset-filters').click(function() {
                $('#status-filter').val('all').selectpicker('refresh');
                $('#search-text-field').val('');
                $('.command-row').show();
                $(this).addClass('d-none');
            });

            // Search functionality
            $('#search-text-field').keyup(function() {
                const searchTerm = $(this).val().toLowerCase();
                const currentStatus = $('#status-filter').val();

                filterCommands(currentStatus, searchTerm);
            });

            // Combined filter function
            function filterCommands(status, searchTerm) {
                // First hide all rows
                $('.command-row').hide();

                // Show reset button if filtering is active
                if (status !== 'all' || searchTerm.length > 0) {
                    $('#reset-filters').removeClass('d-none');
                } else {
                    $('#reset-filters').addClass('d-none');
                    $('.command-row').show();
                    return;
                }

                // Filter by both status and search term
                $('.command-row').each(function() {
                    const $row = $(this);
                    const rowStatus = $row.data('status');
                    const commandType = $row.data('command-type').toLowerCase();
                    const deviceName = $row.find('td:eq(1)').text().toLowerCase();
                    const employeeName = $row.find('td:eq(3)').text().toLowerCase();

                    // Check if row matches search term
                    const matchesSearch = searchTerm.length === 0 ||
                        commandType.includes(searchTerm) ||
                        deviceName.includes(searchTerm) ||
                        employeeName.includes(searchTerm);

                    // Check if row matches status filter
                    const matchesStatus = status === 'all' || rowStatus === status;

                    // Show row if it matches both filters
                    if (matchesSearch && matchesStatus) {
                        $row.show();
                    }
                });
            }
        });
    </script>
@endpush