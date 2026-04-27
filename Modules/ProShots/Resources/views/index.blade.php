@extends('layouts.app')

@section('title', __('proshots::proshots.studio'))

@section('content')
    <div class="content-wrapper">

        <div class="d-flex justify-content-between align-items-center action-bar">
            <h4 class="f-21 font-weight-normal text-capitalize mb-0">
                @lang('proshots::proshots.studio')
            </h4>
            <x-forms.button-primary id="save-form" icon="camera" class="btn-small">
                @lang('proshots::proshots.generate')
            </x-forms.button-primary>
        </div>

        @if (session('message'))
            <div class="alert alert-{{ session('type') === 'success' ? 'success' : 'danger' }} mt-3">
                {{ session('message') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row mt-3">

            {{-- LEFT COLUMN: Upload form --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">

                        <form id="proshots-form"
                              method="POST"
                              action="{{ route('proshots.store') }}"
                              enctype="multipart/form-data">
                            @csrf

                            {{-- Image upload --}}
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-1">
                                    @lang('proshots::proshots.upload_instruction')
                                </label>
                                <div id="proshots-drop-zone"
                                     class="border border-dashed d-flex flex-column align-items-center justify-content-center p-4 rounded cursor-pointer"
                                     ondrop="proshotsDrop(event)"
                                     ondragover="event.preventDefault()"
                                     style="min-height:140px; border-style:dashed !important;">
                                    <i class="fa fa-camera f-24 text-muted mb-2"></i>
                                    <p class="text-muted mb-1 f-13">
                                        @lang('proshots::proshots.upload_instruction')
                                    </p>
                                    <label for="proshots-image" class="btn btn-outline-secondary btn-sm mt-1 mb-0">
                                        @lang('app.browse')
                                    </label>
                                    <input id="proshots-image"
                                           name="image"
                                           type="file"
                                           accept=".png,.jpg,.jpeg"
                                           class="d-none"
                                           onchange="proshotsFileSelected(this)">
                                    <p id="proshots-filename" class="f-11 text-muted mt-1 mb-0"></p>
                                </div>
                                @error('image')
                                    <span class="text-danger f-12">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Background / Theme selector --}}
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-1">
                                    @lang('proshots::proshots.select_background')
                                </label>

                                @if (!empty($themes))
                                    <div class="border rounded p-2" style="max-height:260px; overflow-y:auto;">
                                        <div class="row">
                                            @foreach ($themes as $theme)
                                                <div class="col-6 mb-2">
                                                    <label class="d-block cursor-pointer proshots-theme-card border rounded p-1 text-center"
                                                           style="cursor:pointer;"
                                                           title="{{ $theme['label'] ?? '' }}">
                                                        <input type="radio"
                                                               name="background"
                                                               value="{{ $theme['label'] ?? '' }}"
                                                               class="d-none proshots-theme-radio">
                                                        <img src="{{ $theme['thumbnail'] ?? '' }}"
                                                             alt="{{ $theme['label'] ?? '' }}"
                                                             class="img-fluid rounded mb-1"
                                                             style="height:64px; width:100%; object-fit:cover;">
                                                        <p class="f-11 mb-0 text-truncate">{{ $theme['label'] ?? '' }}</p>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning f-12">
                                        @lang('app.noDataAvailable')
                                        — @lang('proshots::proshots.pebblely_key_help')
                                    </div>
                                @endif

                                @error('background')
                                    <span class="text-danger f-12">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" id="proshots-submit" class="btn btn-primary btn-block">
                                <i class="fa fa-magic mr-1"></i>
                                @lang('proshots::proshots.generate')
                            </button>
                        </form>

                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Before/After preview --}}
            <div class="col-md-8">

                @if ($last)
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">@lang('proshots::proshots.result')</h5>
                            <div>
                                <button id="proshots-toggle-before"
                                        class="btn btn-sm btn-outline-secondary mr-1"
                                        onclick="proshotsToggle(this)">
                                    @lang('proshots::proshots.before')
                                </button>
                                <button id="proshots-toggle-after"
                                        class="btn btn-sm btn-primary active">
                                    @lang('proshots::proshots.after')
                                </button>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <img id="proshots-preview"
                                 src="{{ $last->image }}"
                                 alt="ProShots result"
                                 class="img-fluid rounded shadow"
                                 style="max-height:480px;">
                            <p class="f-11 text-muted mt-2 mb-0">
                                {{ $last->created_at->diffForHumans() }}
                                &nbsp;·&nbsp; 2048 × 2048
                            </p>
                            <div class="mt-2">
                                <a href="{{ $last->image }}"
                                   download
                                   class="btn btn-sm btn-success mr-1">
                                    <i class="fa fa-download mr-1"></i>@lang('app.download')
                                </a>
                                <a href="{{ route('proshots.destroy', $last->id) }}"
                                   onclick="return confirm('{{ __('messages.areYouSure') }}')"
                                   data-method="DELETE"
                                   class="btn btn-sm btn-danger proshots-delete">
                                    <i class="fa fa-trash mr-1"></i>@lang('app.delete')
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- History gallery --}}
                @if ($images->count() > 1)
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">@lang('app.history')</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($images->skip(1) as $item)
                                    <div class="col-6 col-md-4 col-lg-3 mb-3">
                                        <div class="position-relative rounded overflow-hidden border proshots-history-item"
                                             style="aspect-ratio:1; background:#f5f5f5;">
                                            <img src="{{ $item->image }}"
                                                 alt="shot"
                                                 class="img-fluid w-100 h-100"
                                                 style="object-fit:cover;">
                                            <div class="proshots-history-overlay d-flex align-items-center justify-content-center position-absolute"
                                                 style="inset:0; background:rgba(0,0,0,.45); opacity:0; transition:opacity .2s;">
                                                <a href="{{ $item->image }}"
                                                   download
                                                   class="btn btn-sm btn-light mr-1"
                                                   title="@lang('app.download')">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                <a href="{{ route('proshots.destroy', $item->id) }}"
                                                   onclick="return confirm('{{ __('messages.areYouSure') }}')"
                                                   data-method="DELETE"
                                                   class="btn btn-sm btn-danger proshots-delete"
                                                   title="@lang('app.delete')">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <p class="f-10 text-muted mt-1 mb-0 text-truncate">
                                            {{ $item->created_at->format('d M Y') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>
@endsection

@push('styles')
<style>
    .proshots-theme-card.selected {
        border-color: var(--blue) !important;
        box-shadow: 0 0 0 2px rgba(var(--blue-rgb),.35);
    }
    .proshots-history-item:hover .proshots-history-overlay {
        opacity: 1;
    }
    #proshots-drop-zone.drag-over {
        background: rgba(0,123,255,.05);
    }
</style>
@endpush

@push('scripts')
<script>
    // Drag-and-drop
    function proshotsDrop(event) {
        event.preventDefault();
        const file = event.dataTransfer.files[0];
        if (file) {
            document.getElementById('proshots-image').files = event.dataTransfer.files;
            document.getElementById('proshots-filename').textContent = file.name;
        }
    }

    function proshotsFileSelected(input) {
        if (input.files.length) {
            document.getElementById('proshots-filename').textContent = input.files[0].name;
        }
    }

    // Theme card selection
    document.querySelectorAll('.proshots-theme-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.proshots-theme-card').forEach(function (c) {
                c.classList.remove('selected');
            });
            card.classList.add('selected');
            card.querySelector('.proshots-theme-radio').checked = true;
        });
    });

    // Before/After toggle (placeholder — original image not stored separately in v2.0.0)
    function proshotsToggle(btn) {
        // ProShots v2.0.0 stores only the AI output; toggle is a UI hint only.
        document.getElementById('proshots-toggle-before').classList.toggle('btn-primary');
        document.getElementById('proshots-toggle-before').classList.toggle('btn-outline-secondary');
        document.getElementById('proshots-toggle-after').classList.toggle('btn-primary');
        document.getElementById('proshots-toggle-after').classList.toggle('btn-outline-secondary');
    }

    // DELETE via form spoofing (Laravel requires DELETE method)
    document.querySelectorAll('.proshots-delete').forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (!confirm('{{ __('messages.areYouSure') }}')) {
                e.preventDefault();
                return false;
            }
            e.preventDefault();
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = link.href;
            const csrf = document.createElement('input');
            csrf.type  = 'hidden';
            csrf.name  = '_token';
            csrf.value = '{{ csrf_token() }}';
            const method = document.createElement('input');
            method.type  = 'hidden';
            method.name  = '_method';
            method.value = 'DELETE';
            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        });
    });

    // Loading state on submit
    document.getElementById('proshots-form').addEventListener('submit', function () {
        const btn = document.getElementById('proshots-submit');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> @lang('app.processing')...';
    });
</script>
@endpush
