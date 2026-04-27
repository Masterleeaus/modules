@extends('layouts.app')

@section('title', __('proshots::proshots.settings'))

@section('content')
    <div class="content-wrapper">

        <div class="d-flex justify-content-between align-items-center action-bar">
            <h4 class="f-21 font-weight-normal text-capitalize mb-0">
                @lang('proshots::proshots.settings')
            </h4>
        </div>

        @if (session('message'))
            <div class="alert alert-{{ session('type') === 'success' ? 'success' : 'danger' }} mt-3">
                {{ session('message') }}
            </div>
        @endif

        <div class="card mt-3">
            <div class="card-body">
                <form method="POST" action="{{ route('proshots.settings.update') }}">
                    @csrf

                    <div class="form-group">
                        <label class="f-14 text-dark-grey" for="pebblely_key">
                            @lang('proshots::proshots.pebblely_key')
                            <span class="text-danger">*</span>
                        </label>
                        <input id="pebblely_key"
                               name="pebblely_key"
                               type="text"
                               class="form-control @error('pebblely_key') is-invalid @enderror"
                               value="{{ old('pebblely_key', $pebblelyKey ?? '') }}"
                               placeholder="pk_live_...">
                        @error('pebblely_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <a href="https://pebblely.com/docs/" target="_blank" rel="noopener">
                                @lang('proshots::proshots.pebblely_key_help')
                            </a>
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save mr-1"></i> @lang('proshots::proshots.save')
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection
