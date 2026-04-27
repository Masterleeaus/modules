@extends('layouts.app')

@section('content')
    <div class="content-wrapper" style="padding: 12px;">
        <div class="card bg-white border-0 b-shadow-4" style="max-width: 720px; margin: 0 auto;">
            <div class="card-header bg-white border-bottom-grey p-20">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="f-12 text-dark-grey">Equipment Scan</div>
                        <h3 class="heading-h1 mb-1">{{ $asset->name }}</h3>
                        <div class="f-13 text-dark-grey">{{ optional($asset->assetType)->name }}</div>
                    </div>

                    @php
                        $class = \Modules\Asset\Entities\Asset::STATUSES;
                        $badge = '<i class="fa fa-circle mr-1 '.$class[$asset->status].' f-10"></i>'.__('asset::app.'.$asset->status);
                    @endphp

                    <div class="text-right">
                        <div class="badge badge-light border">{!! $badge !!}</div>
                        <div class="f-12 text-dark-grey mt-2">{{ $asset->location ?: '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-7">
                        <div class="mb-3">
                            <div class="f-12 text-dark-grey">Serial</div>
                            <div class="f-16">{{ $asset->serial_number ?: '—' }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="f-12 text-dark-grey">Last Activity</div>
                            <div class="f-16">
                                @if($asset->latestHistory)
                                    {{ $asset->latestHistory->created_at?->format('d M Y, g:ia') }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="javascript:;" data-asset-id="{{ $asset->id }}" class="btn btn-primary btn-lg w-100 mb-2 lend">
                                Issue / Lend
                            </a>

                            <a href="javascript:;" data-asset-id="{{ $asset->id }}"
                               data-history-id="{{ $asset->latestHistory?->id }}"
                               class="btn btn-success btn-lg w-100 mb-2 returnAsset">
                                Return
                            </a>

                            <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-danger btn-lg w-100">
                                Report Damage / Missing
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-md-5 mt-4 mt-md-0">
                        @if ($asset->image_url)
                            <div class="mb-3">
                                <a target="_blank" href="{{ $asset->image_url }}" class="text-darkest-grey">
                                    <img src="{{ $asset->image_url }}" style="width:100%; border-radius: 10px;" />
                                </a>
                            </div>
                        @endif

                        <div class="card border-0 bg-light" style="padding: 12px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="f-14"><strong>QR Code</strong></div>
                                @if(!$asset->qrcode_id)
                                    <button type="button" class="btn btn-sm btn-outline-primary generateQr" data-asset-id="{{ $asset->id }}">Generate</button>
                                @endif
                            </div>

                            <div class="mt-2 text-center">
                                @if($asset->qrcode_id && Route::has('qrcode.download'))
                                    <img alt="QR" src="{{ route('qrcode.download', [$asset->qrcode_id, 'png']) }}" style="width: 220px; max-width: 100%;" />
                                    <div class="mt-2">
                                        <a class="btn btn-sm btn-outline-dark" href="{{ route('qrcode.download', [$asset->qrcode_id, 'png']) }}">Download PNG</a>
                                    </div>
                                @else
                                    <div class="f-12 text-dark-grey">No QR attached yet.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('body').on('click', '.lend', function () {
            let id = $(this).data('asset-id');
            let url = "{{ route('history.create', ':id') }}";
            url = url.replace(':id', id);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').on('click', '.returnAsset', function () {
            let id = $(this).data('asset-id');
            let historyId = $(this).data('history-id');

            if (!historyId) {
                Swal.fire({
                    title: "Can't return",
                    text: "No active lending record was found for this equipment.",
                    icon: 'info',
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }

            let url = "{{ route('assets.return', [':asset', ':history']) }}";
            url = url.replace(':asset', id);
            url = url.replace(':history', historyId);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').on('click', '.generateQr', function () {
            let id = $(this).data('asset-id');
            let url = "{{ route('assets.qr.generate', ':id') }}";
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                type: "POST",
                container: '#payroll-detail-section',
                data: { _token: "{{ csrf_token() }}" },
                success: function (response) {
                    if (response.status === 'success') {
                        window.location.reload();
                    }
                }
            });
        });
    </script>
@endpush


<div class="mt-3 d-grid gap-2">
    <form method="POST" action="{{ route('assets.scan.sendMaintenance', $asset->id) }}">
        @csrf
        <button type="submit" class="btn btn-warning btn-lg w-100">Send to Maintenance</button>
    </form>

    <form method="POST" action="{{ route('assets.scan.completeMaintenance', $asset->id) }}">
        @csrf
        <button type="submit" class="btn btn-success btn-lg w-100">Complete Maintenance</button>
    </form>

    <form method="POST" action="{{ route('assets.scan.allocate', $asset->id) }}">
        @csrf
        <button type="submit" class="btn btn-primary btn-lg w-100">Allocate</button>
    </form>

    <form method="POST" action="{{ route('assets.scan.revokeAllocation', $asset->id) }}">
        @csrf
        <button type="submit" class="btn btn-secondary btn-lg w-100">Revoke Allocation</button>
    </form>
</div>
