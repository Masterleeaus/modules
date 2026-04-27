@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center action-bar flex-wrap">
            <div>
                <h4 class="f-21 font-weight-normal text-capitalize mb-0">
                    {{ $document->title }}
                    <span class="badge badge-{{ ['draft'=>'secondary','in_review'=>'warning','approved'=>'success','rejected'=>'danger','archived'=>'dark'][$document->status] ?? 'secondary' }} f-14 ml-1">
                        {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                    </span>
                    <small class="text-muted f-14 ml-1">v{{ $document->version }}</small>
                </h4>
            </div>
            <div class="d-flex gap-2 mt-2 mt-lg-0">
                @if($this->user->permission('edit_vault_documents') && $document->status !== 'archived')
                    <a href="{{ route('titan-vault.documents.edit', $document->id) }}"
                       class="btn btn-outline-secondary btn-sm mr-1">
                        <i class="fa fa-edit mr-1"></i> @lang('app.edit')
                    </a>
                    @if($document->status === 'draft')
                        <button class="btn btn-primary btn-sm mr-1" id="btn-send-review">
                            <i class="fa fa-paper-plane mr-1"></i> @lang('titan_vault::titan_vault.send_for_review')
                        </button>
                    @endif
                    @if($document->status !== 'archived')
                        <button class="btn btn-outline-dark btn-sm mr-1" id="btn-archive">
                            <i class="fa fa-archive mr-1"></i> @lang('titan_vault::titan_vault.archive')
                        </button>
                    @endif
                @endif
                <a href="{{ route('titan-vault.documents.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left mr-1"></i> @lang('app.back')
                </a>
            </div>
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs mt-3" id="docTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview">
                    @lang('titan_vault::titan_vault.overview')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="versions-tab" data-toggle="tab" href="#versions">
                    @lang('titan_vault::titan_vault.versions')
                    <span class="badge badge-secondary">{{ $document->versions->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="comments-tab" data-toggle="tab" href="#comments">
                    @lang('titan_vault::titan_vault.comments')
                    <span class="badge badge-secondary">{{ $document->comments->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="approvals-tab" data-toggle="tab" href="#approvals">
                    @lang('titan_vault::titan_vault.approvals')
                    <span class="badge badge-secondary">{{ $document->approvals->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="links-tab" data-toggle="tab" href="#links">
                    @lang('titan_vault::titan_vault.share_links')
                    <span class="badge badge-secondary">{{ $document->accessLinks->count() }}</span>
                </a>
            </li>
        </ul>

        <div class="tab-content mt-3">
            {{-- Overview --}}
            <div class="tab-pane fade show active" id="overview">
                <div class="card">
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">@lang('titan_vault::titan_vault.title')</dt>
                            <dd class="col-sm-9">{{ $document->title }}</dd>

                            <dt class="col-sm-3">@lang('titan_vault::titan_vault.description')</dt>
                            <dd class="col-sm-9">{{ $document->description ?: '—' }}</dd>

                            <dt class="col-sm-3">@lang('titan_vault::titan_vault.created_by')</dt>
                            <dd class="col-sm-9">{{ optional($document->creator)->name ?? '—' }}</dd>

                            <dt class="col-sm-3">@lang('app.createdAt')</dt>
                            <dd class="col-sm-9">{{ $document->created_at->format('d M Y H:i') }}</dd>

                            @if($document->expires_at)
                                <dt class="col-sm-3">@lang('titan_vault::titan_vault.expires_at')</dt>
                                <dd class="col-sm-9">{{ $document->expires_at->format('d M Y H:i') }}</dd>
                            @endif
                        </dl>

                        @if($document->content)
                            <hr>
                            <h6 class="text-dark-grey">@lang('titan_vault::titan_vault.content')</h6>
                            <div class="border rounded p-3 bg-light f-14" style="white-space: pre-wrap;">{{ $document->content }}</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Versions --}}
            <div class="tab-pane fade" id="versions">
                <div class="card">
                    <div class="card-body">
                        @forelse($document->versions->sortByDesc('version_number') as $ver)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <strong>v{{ $ver->version_number }}</strong>
                                    <span class="text-muted f-13 ml-2">
                                        {{ optional($ver->creator)->name ?? '—' }} &bull;
                                        {{ $ver->created_at->format('d M Y H:i') }}
                                    </span>
                                    @if($ver->notes)
                                        <p class="mb-0 f-13 text-muted">{{ $ver->notes }}</p>
                                    @endif
                                </div>
                                @if($this->user->permission('edit_vault_documents'))
                                    <form method="POST"
                                          action="{{ route('titan-vault.documents.versions.restore', [$document->id, $ver->id]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-undo mr-1"></i> @lang('titan_vault::titan_vault.restore')
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted mb-0">@lang('titan_vault::titan_vault.no_versions')</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Comments --}}
            <div class="tab-pane fade" id="comments">
                <div class="card">
                    <div class="card-body">
                        @forelse($document->comments->whereNull('parent_comment_id') as $comment)
                            <div class="border rounded p-3 mb-2 {{ $comment->resolved_at ? 'bg-light' : '' }}">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ optional($comment->user)->name ?? 'Client' }}</strong>
                                    <div>
                                        @if($comment->resolved_at)
                                            <span class="badge badge-success">@lang('titan_vault::titan_vault.resolved')</span>
                                        @else
                                            @if($this->user->permission('edit_vault_documents'))
                                                <form method="POST" class="d-inline"
                                                      action="{{ route('titan-vault.comments.resolve', [$document->id, $comment->id]) }}">
                                                    @csrf
                                                    <button class="btn btn-xs btn-outline-success">
                                                        @lang('titan_vault::titan_vault.resolve')
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <p class="mb-0 f-14 mt-1">{{ $comment->content }}</p>
                                <small class="text-muted">{{ $comment->created_at->format('d M Y H:i') }}</small>
                            </div>
                        @empty
                            <p class="text-muted mb-0">@lang('titan_vault::titan_vault.no_comments')</p>
                        @endforelse

                        {{-- Add comment form --}}
                        <form method="POST"
                              action="{{ route('titan-vault.comments.store', $document->id) }}"
                              class="mt-3">
                            @csrf
                            <div class="form-group">
                                <textarea name="content" rows="3" class="form-control f-14"
                                          placeholder="@lang('titan_vault::titan_vault.add_comment_placeholder')"
                                          required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fa fa-comment mr-1"></i> @lang('titan_vault::titan_vault.add_comment')
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Approvals --}}
            <div class="tab-pane fade" id="approvals">
                <div class="card">
                    <div class="card-body">
                        @forelse($document->approvals as $approval)
                            <div class="d-flex justify-content-between align-items-start border-bottom py-2">
                                <div>
                                    <strong>{{ $approval->approver_name }}</strong>
                                    <span class="text-muted f-13">&lt;{{ $approval->approver_email }}&gt;</span>
                                    <span class="badge badge-{{ $approval->action === 'approved' ? 'success' : 'warning' }} ml-1">
                                        {{ ucfirst(str_replace('_', ' ', $approval->action)) }}
                                    </span>
                                    @if($approval->revision_notes)
                                        <p class="mb-0 f-13 text-muted mt-1">{{ $approval->revision_notes }}</p>
                                    @endif
                                    <small class="text-muted d-block">{{ optional($approval->approved_at ?? $approval->created_at)->format('d M Y H:i') }}</small>
                                </div>
                                @if($this->user->permission('delete_vault_documents'))
                                    <form method="POST"
                                          action="{{ route('titan-vault.approvals.destroy', [$document->id, $approval->id]) }}">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-outline-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted mb-0">@lang('titan_vault::titan_vault.no_approvals')</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Share Links --}}
            <div class="tab-pane fade" id="links">
                <div class="card">
                    <div class="card-body">
                        @forelse($document->accessLinks as $link)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <code class="f-13">{{ route('titan-vault.proof.show', $link->token) }}</code>
                                    <span class="badge badge-{{ $link->is_active ? 'success' : 'secondary' }} ml-1">
                                        {{ $link->is_active ? __('app.active') : __('app.inactive') }}
                                    </span>
                                    @if($link->expires_at)
                                        <small class="text-muted ml-1">
                                            @lang('titan_vault::titan_vault.expires'): {{ $link->expires_at->format('d M Y') }}
                                        </small>
                                    @endif
                                    <small class="text-muted d-block">
                                        {{ $link->view_count }} views &bull;
                                        @lang('titan_vault::titan_vault.created_by'): {{ optional($link->creator)->name ?? '—' }}
                                    </small>
                                </div>
                                @if($link->is_active && $this->user->permission('edit_vault_documents'))
                                    <form method="POST"
                                          action="{{ route('titan-vault.links.revoke', [$document->id, $link->id]) }}">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-outline-danger">
                                            @lang('titan_vault::titan_vault.revoke')
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted mb-0">@lang('titan_vault::titan_vault.no_links')</p>
                        @endforelse

                        {{-- Generate new link --}}
                        @if($this->user->permission('edit_vault_documents'))
                            <form method="POST"
                                  action="{{ route('titan-vault.links.generate', $document->id) }}"
                                  class="mt-3">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="f-14">@lang('titan_vault::titan_vault.expires_at')</label>
                                            <input type="date" name="expires_at" class="form-control f-14">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="f-14">@lang('titan_vault::titan_vault.password') <small class="text-muted">(optional)</small></label>
                                            <input type="password" name="password" class="form-control f-14">
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <div class="form-group w-100">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fa fa-link mr-1"></i> @lang('titan_vault::titan_vault.generate_link')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Send for review
    $('#btn-send-review').on('click', function () {
        $.ajax({
            url: '{{ route('titan-vault.documents.send-for-review', $document->id) }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                if (res.status === 'success') {
                    const url = res.proof_url || '';
                    const msg = '@lang('titan_vault::titan_vault.sent_for_review')' + (url ? '\n\n' + url : '');
                    toastr.success(msg);
                    setTimeout(function () { window.location.reload(); }, 2000);
                }
            }
        });
    });

    // Archive
    $('#btn-archive').on('click', function () {
        if (!confirm('@lang('titan_vault::titan_vault.confirm_archive')')) return;
        $.ajax({
            url: '{{ route('titan-vault.documents.archive', $document->id) }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function () { window.location.reload(); }
        });
    });
</script>
@endpush
