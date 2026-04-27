<div id="payroll-detail-section">
    <div class="row">
        <div class="col-sm-12">
            <div class="card bg-white border-0 b-shadow-4">
                <div class="card-header bg-white border-bottom-grey  justify-content-between p-20">
                    <div class="row">
                        <div class="col-md-10">
                            <h3 class="heading-h1 mb-3">@lang('asset::app.assetInfo')</h3>
                        </div>
                        @if (user()->permission('edit_asset') == 'all' || user()->permission('edit_asset') == 'added')
                            <div class="col-md-2 text-right">
                                <div class="dropdown">
                                    <button
                                        class="btn btn-lg f-14 px-2 py-1 text-dark-grey  rounded dropdown-toggle"
                                        type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-h"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                         aria-labelledby="dropdownMenuLink" tabindex="0">

                                        <a class="dropdown-item openRightModal"
                                           href="{{ route('assets.edit', $asset->id) }}">@lang('app.edit')</a>

                                        @if ($asset->status == 'available')
                                            <a href="javascript:;" data-asset-id="{{ $asset->id }}"
                                               class="dropdown-item lend">
                                                {{ trans('asset::app.lend') }}</a>
                                        @endif

                                        @if ($asset->status == 'lent')
                                            <a href="javascript:;" data-asset-id="{{ $asset->id }}"
                                               data-history-id="{{ $asset->history->count() > 0 ? $asset->history[0]->id : '' }}"
                                               class="dropdown-item returnAsset">
                                                {{ trans('asset::app.return') }}</a>
                                        @endif

                                    </div>

                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-10">
                            <x-cards.data-row :label="__('asset::app.assetName')" :value="$asset->name"
                                              html="true"/>
                            <x-cards.data-row :label="__('asset::app.assetType')"
                                              :value="ucwords($asset->assetType->name)" html="true"/>
                            @php
                                $class = \Modules\Asset\Entities\Asset::STATUSES;
                                $status = '<i class="fa fa-circle mr-1 '.$class[$asset->status].' f-10"></i>'.__('asset::app.'.$asset->status);
                            @endphp

                            <x-cards.data-row :label="__('asset::app.status')" :value="$status"
                                              html="true"/>
                            <x-cards.data-row :label="__('asset::app.serialNumber')" :value="$asset->serial_number"
                                              html="true"/>
                            <x-cards.data-row :label="__('asset::app.value')" :value="$asset->value" html="true"/>
                            <x-cards.data-row :label="__('asset::app.location')" :value="$asset->location"
                                              html="true"/>
                        </div>
                        <div class="col-2">
                            @if ($asset->image_url)
                                <a target="_blank" href="{{ $asset->image_url }}" class="text-darkest-grey">
                                    <img src="{{ $asset->image_url }}"/>
                                </a>
                            @endif
                        </div>

                    </div>
                    <div class="row mt-4" id="history">
                        @include($history)
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('body').on('click', '.edit-history', function () {
        var historyId = $(this).data('history-id');
        var assetId = $(this).data('asset-id');
        var url = "{{ route('history.edit', [':assetId', ':historyId']) }}";
        url = url.replace(':historyId', historyId);
        url = url.replace(':assetId', assetId);
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });
    $('body').on('click', '.delete-history', function () {
        var historyId = $(this).data('history-id');
        var assetId = $(this).data('asset-id');
        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.recoverRecord')",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('messages.confirmDelete')",
            cancelButtonText: "@lang('app.cancel')",
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('history.destroy', [':assetId', ':historyId']) }}";
                url = url.replace(':historyId', historyId);
                url = url.replace(':assetId', assetId);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            $('#history').html(response.view);
                        }
                    }
                });
            }
        });
    });
</script>
