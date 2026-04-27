@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="d-flex justify-content-between align-items-center action-bar">
            <h4 class="f-21 font-weight-normal text-capitalize mb-0">
                @lang('titan_vault::titan_vault.edit_document')
            </h4>
            <a href="{{ route('titan-vault.documents.show', $document->id) }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-arrow-left mr-1"></i> @lang('app.back')
            </a>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <form id="edit-document-form" method="POST"
                      action="{{ route('titan-vault.documents.update', $document->id) }}"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <x-forms.label fieldId="title" :fieldLabel="__('titan_vault::titan_vault.title')" fieldRequired="true" />
                        <input type="text" id="title" name="title"
                               class="form-control f-14 @error('title') is-invalid @enderror"
                               value="{{ old('title', $document->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <x-forms.label fieldId="description" :fieldLabel="__('titan_vault::titan_vault.description')" />
                        <textarea id="description" name="description" rows="3"
                                  class="form-control f-14">{{ old('description', $document->description) }}</textarea>
                    </div>

                    <div class="form-group">
                        <x-forms.label fieldId="file" :fieldLabel="__('titan_vault::titan_vault.upload_file')" />
                        <input type="file" id="file" name="file" class="form-control-file f-14">
                        @if($document->file_path)
                            <small class="text-muted">
                                @lang('titan_vault::titan_vault.current_file'): <code>{{ $document->file_path }}</code>
                            </small>
                        @endif
                    </div>

                    <div class="form-group">
                        <x-forms.label fieldId="content" :fieldLabel="__('titan_vault::titan_vault.content')" />
                        <textarea id="content" name="content" rows="6"
                                  class="form-control f-14">{{ old('content', $document->content) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-forms.label fieldId="project_id" :fieldLabel="__('titan_vault::titan_vault.project')" />
                                <input type="number" id="project_id" name="project_id"
                                       class="form-control f-14"
                                       value="{{ old('project_id', $document->project_id) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-forms.label fieldId="client_id" :fieldLabel="__('titan_vault::titan_vault.client')" />
                                <input type="number" id="client_id" name="client_id"
                                       class="form-control f-14"
                                       value="{{ old('client_id', $document->client_id) }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <x-forms.label fieldId="status" :fieldLabel="__('titan_vault::titan_vault.status')" />
                        <select id="status" name="status" class="form-control f-14">
                            @foreach(['draft', 'in_review', 'approved', 'rejected', 'archived'] as $s)
                                <option value="{{ $s }}" {{ old('status', $document->status) === $s ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <x-forms.label fieldId="expires_at" :fieldLabel="__('titan_vault::titan_vault.expires_at')" />
                        <input type="datetime-local" id="expires_at" name="expires_at"
                               class="form-control f-14"
                               value="{{ old('expires_at', optional($document->expires_at)->format('Y-m-d\TH:i')) }}">
                    </div>

                    <div class="form-group mt-3">
                        <x-forms.button-primary id="update-document" icon="check">
                            @lang('app.update')
                        </x-forms.button-primary>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
