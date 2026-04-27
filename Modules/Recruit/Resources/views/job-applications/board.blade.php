@php
    $addApplicationPermission = user()->permission('add_job_application');
    $addStatusPermission = user()->permission('add_application_status');
    $editStatusPermission = user()->permission('edit_application_status');
    $changeStatusPermission = user()->permission('change_application_status');
    $deleteStatusPermission = user()->permission('delete_application_status');
@endphp

@foreach ($result['boardColumns'] as $key => $column)
    @if ($column->userSetting && $column->userSetting->collapsed)
        <div class="minimized rounded bg-additional-grey border-grey mr-3">
            <!-- TASK BOARD HEADER START -->
            <div class="d-flex mt-4 mx-1 b-p-header align-items-center">
                <a href="javascript:;" class="d-grid f-8 mb-3 text-lightest collapse-column"
                    data-column-id="{{ $column->id }}" data-type="maximize" data-toggle="tooltip" data-original-title=@lang('app.expand')>
                    <i class="fa fa-chevron-right ml-1"></i>
                    <i class="fa fa-chevron-left"></i>
                </a>

                <p class="mb-3 mx-0 f-15 text-dark-grey font-weight-bold"><i class="fa fa-circle mb-2 text-red"
                    style="color: {{ $column->color }}"></i>{{ ($column->status) }}
                </p>

                <span
                    class="b-p-badge bg-grey f-13 px-2 py-2 text-lightest font-weight-bold rounded d-inline-block">{{ $column->applications_count }}</span>
            </div>
            <!-- TASK BOARD HEADER END -->
        </div>
    @else
        <!-- BOARD PANEL 2 START -->
        <div class="board-panel rounded bg-additional-grey border-grey mr-3 ">
            <!-- TASK BOARD HEADER START -->
            <div class="d-flex m-3 b-p-header">
                <p class="mb-0 f-15 mr-3 text-dark-grey font-weight-bold"><i class="fa fa-circle mr-2 text-yellow"
                                                                            style="color: {{ $column->color }}"></i>{{ substr(($column->status),0,25) }}
                </p>
                <span
                    class="b-p-badge bg-grey f-13 px-2 text-lightest font-weight-bold rounded d-inline-block">{{ $column->applications_count }}</span>

                <span class="ml-auto d-flex align-items-center">
                    <a href="javascript:;" class="d-flex f-8 text-lightest collapse-column"
                        data-column-id="{{ $column->id }}" data-type="minimize" data-toggle="tooltip" data-original-title=@lang('app.collapse')>
                        <i class="fa fa-chevron-right mr-1"></i>
                        <i class="fa fa-chevron-left"></i>
                    </a>
                    @if ($addApplicationPermission == 'all' || $addApplicationPermission == 'added' || $addStatusPermission == 'all')
                        <div class="dropdown">
                            <button
                                class="btn bg-white btn-lg f-10 px-2 py-1 text-dark-grey  rounded  dropdown-toggle ml-3"
                                type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="fa fa-ellipsis-h"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                 aria-labelledby="dropdownMenuLink" tabindex="0">
                                @if ($addApplicationPermission == 'all' || $addApplicationPermission == 'added')
                                    <a class="dropdown-item openRightModal"
                                       href="{{ route('job-applications.create') }}?status_id={{ $column->id }}">@lang('app.add')
                                        @lang('recruit::app.menu.jobApplication')</a>
                                @endif

                                @if ($editStatusPermission == 'all')
                                    <hr class="my-1">
                                    <a class="dropdown-item edit-column-board" data-column-id="{{ $column->id }}"
                                       href="javascript:;">@lang('app.edit')</a>
                                @endif
                                @if($deleteStatusPermission == 'all' && $column->id != 1 && $column->id != 2 && $column->id != 3 && $column->id != 4 && $column->id != 5)
                                    <a class="dropdown-item delete-column" data-column-id="{{ $column->id }}"
                                       href="javascript:;">@lang('app.delete')</a>
                                @endif
                            </div>
                        </div>
                    @endif

                </span>
            </div>
            <!-- TASK BOARD BODY START -->
            <div class="b-p-body">
                <!-- MAIN TASKS START -->
                <div class="b-p-tasks" id="drag-container-{{ $column->id }}"
                     data-column-id="{{ $column->id }}">

                    @forelse ($column['applications'] as $application)
                        <x-recruit::cards.job-card :draggable="$changeStatusPermission == 'all' ? 'true' : 'false'"
                                                   :application="$application"/>
                    @empty
                        @if ($column->applications_count == 0)
                            <div
                                class="card rounded bg-white border-grey b-shadow-4 m-1 mb-3 no-task-card move-disable">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center py-3">
                                        <p class="mb-0">
                                            @if ($addApplicationPermission == 'all' || $addApplicationPermission == 'added')
                                                <a href="{{ route('job-applications.create') }}?status_id={{ $column->id }}"
                                                   class="text-dark-grey openRightModal"><i
                                                        class="fa fa-plus mr-2"></i>@lang('app.add')
                                                    @lang('recruit::app.menu.jobApplication')</a>
                                        @else
                                            <div class="align-items-center d-flex flex-column text-lightest w-100">
                                                <i class="fa fa-tasks f-15 w-100"></i>
                                                <div class="f-15 mt-4">
                                                    - @lang('messages.noRecordFound') -
                                                </div>
                                            </div>
                                        @endif
                                        </p>
                                    </div>
                                </div>
                            </div><!-- div end -->
                        @endif
                    @endforelse

                </div>

            @if ($column->applications_count > count($column['applications']))
                <!-- TASK BOARD FOOTER START -->
                    <div class="d-flex m-3 justify-content-center">
                        <a class="f-13 text-dark-grey f-w-500 load-more-tasks" data-column-id="{{ $column->id }}"
                           data-total-tasks="{{ $column->applications_count }}"
                           href="javascript:;">@lang('modules.tasks.loadMore')</a>
                    </div>
                    <!-- TASK BOARD FOOTER END -->
                @endif
            </div>
            <!-- TASK BOARD BODY END -->
        </div>
        <!-- BOARD PANEL 2 END -->
    @endif

@endforeach

<!-- Drag and Drop Plugin -->
<script>
    var arraylike = document.getElementsByClassName('b-p-tasks');
    var containers = Array.prototype.slice.call(arraylike);
    var drake = dragula({
        containers: containers,
        moves: function (el, source, handle, sibling) {
            if (el.classList.contains('move-disable')) {
                return false;
            }

            return true; // elements are always draggable by default
        },
    })
        .on('drag', function (el) {
            el.className = el.className.replace('ex-moved', '');
        }).on('drop', function (el) {
            el.className += ' ex-moved';
        }).on('over', function (el, container) {
            container.className += ' ex-over';
        }).on('out', function (el, container) {
            container.className = container.className.replace('ex-over', '');
        });
</script>

<script>
    drake.on('drop', function (element, target, source, sibling) {
        var elementId = element.id;
        $children = $('#' + target.id).children();
        var boardColumnId = $('#' + target.id).data('column-id');
        var movingAppId = $('#' + element.id).data('app-id');

        var applicationIds = [];
        var prioritys = [];

        $children.each(function (ind, el) {
            applicationIds.push($(el).data('app-id'));
            prioritys.push($(el).index());
        });

        $.easyAjax({
            url: "{{ route('job-appboard.update_index') }}",
            type: 'POST',
            container: '#taskboard-columns',
            blockUI: true,
            data: {
                boardColumnId: boardColumnId,
                movingAppId: movingAppId,
                applicationIds: applicationIds,
                prioritys: prioritys,
                '_token': '{{ csrf_token() }}'
            },
            success: function (response) {

                let app_id = movingAppId;
                let board = 1;

                if (app_id && response.board.action == 'yes') {
                    if (response.board.category.name == 'shortlist') {
                        var url = "{{ route('job-appboard.application_remark', [':id', ':board']) }}";
                        url = url.replace(':id', app_id);
                        url = url.replace(':board', board);

                        $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
                        $.ajaxModal(MODAL_DEFAULT, url);
                    }
                    if (response.board.category.name == 'interview') {
                        var url = "{{ route('job-appboard.interview', [':id', ':board']) }}";
                        url = url.replace(':id', app_id);
                        url = url.replace(':board', board);
                        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                        $.ajaxModal(MODAL_LG, url);
                    }
                    if (response.board.category.name == 'hired') {
                        var url = "{{ route('job-appboard.offer_letter', [':id', ':board']) }}";
                        url = url.replace(':id', app_id);
                        url = url.replace(':board', board);
                        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                        $.ajaxModal(MODAL_LG, url);
                    }
                    if (response.board.category.name == 'rejected') {
                        var url = "{{ route('job-appboard.rejected_remark', [':id', ':board']) }}";
                        url = url.replace(':id', app_id);
                        url = url.replace(':board', board);
                        $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
                        $.ajaxModal(MODAL_DEFAULT, url);
                    }
                }

                if ($('#' + source.id + ' .task-card').length == 0) {
                    $('#' + source.id + ' .no-task-card').removeClass('d-none');
                }
                if ($('#' + target.id + ' .task-card').length > 0) {
                    $('#' + target.id + ' .no-task-card').addClass('d-none');
                }
            }
        });

    });
</script>


<script>
    $('.close-application').click(function() {
        var elementId = 'drag-task-'+$(this).data('app-id');
        $children = $('#drag-container-{{ $rejectColumn->id }}').children();
        var boardColumnId = $('#drag-container-{{ $rejectColumn->id }}').data('column-id');
        var movingAppId = $('#' + elementId).data('app-id');

        var applicationIds = [];
        var prioritys = [];

        $children.each(function (ind, el) {
            applicationIds.push($(el).data('app-id'));
            prioritys.push($(el).index());
        });

        applicationIds.push(movingAppId);
        prioritys.push(prioritys.length);


        $.easyAjax({
            url: "{{ route('job-appboard.update_index') }}",
            type: 'POST',
            container: '#taskboard-columns',
            blockUI: true,
            data: {
                boardColumnId: boardColumnId,
                movingAppId: movingAppId,
                applicationIds: applicationIds,
                prioritys: prioritys,
                '_token': '{{ csrf_token() }}'
            },
            success: function (response) {

                if ($('#drag-task-' + elementId + ' .task-card').length == 0) {
                    $('#drag-task-' + elementId + ' .no-task-card').removeClass('d-none');
                }
                if ($('#drag-container-{{ $rejectColumn->id }} .task-card').length > 0) {
                    $('#drag-container-{{ $rejectColumn->id }} .no-task-card').addClass('d-none');
                }

                loadData();
            }
        });

    });
</script>
