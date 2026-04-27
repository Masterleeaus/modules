@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="d-flex justify-content-between align-items-center action-bar">
            <h4 class="f-21 font-weight-normal text-capitalize mb-0">
                @lang('titan_vault::titan_vault.settings')
            </h4>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <form id="vault-settings-form" method="POST"
                      action="{{ route('titan-vault.settings.update') }}">
                    @csrf

                    <div class="form-group">
                        <x-forms.label fieldId="storage_disk"
                            :fieldLabel="__('titan_vault::titan_vault.storage_disk')" fieldRequired="true" />
                        <select id="storage_disk" name="storage_disk" class="form-control f-14">
                            @foreach(['local', 's3', 'public'] as $disk)
                                <option value="{{ $disk }}"
                                    {{ ($storageDisk ?? 'local') === $disk ? 'selected' : '' }}>
                                    {{ strtoupper($disk) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <x-forms.label fieldId="default_expiry_days"
                            :fieldLabel="__('titan_vault::titan_vault.default_expiry_days')" fieldRequired="true" />
                        <input type="number" id="default_expiry_days" name="default_expiry_days"
                               class="form-control f-14"
                               value="{{ $defaultExpiryDays ?? 30 }}"
                               min="0" required>
                        <small class="text-muted">@lang('titan_vault::titan_vault.expiry_days_hint')</small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="require_password"
                                   name="require_password" value="1"
                                   {{ ($requirePassword ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label f-14" for="require_password">
                                @lang('titan_vault::titan_vault.require_password')
                            </label>
                        </div>
                        <small class="text-muted">@lang('titan_vault::titan_vault.require_password_hint')</small>
                    </div>

                    <div class="form-group mt-4">
                        <x-forms.button-primary id="save-settings" icon="check">
                            @lang('app.save')
                        </x-forms.button-primary>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
