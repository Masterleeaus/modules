@php
  @endphp
@extends('layouts/layoutMaster')

@section('title', __('Expense Details'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
  ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endsection

@section('page-script')
  @vite([
  'resources/js/main-datatable.js',
  'resources/js/main-select2.js',
  'resources/assets/js/app/expenseType-view.js'
  ])
@endsection

@section('content')
  <div class="row g-4">
    <!-- Left Column: Expense Type Details -->
    <div class="col-12 col-md-4">
      <div class="card">
        <div class="card-header d-flex align-items-center">
          <h5 class="mb-0">@lang('Expense Type Details')</h5>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex align-items-center mb-3">
              <i class="bx bx-receipt me-2"></i>
              <strong>@lang('Name'):</strong> <span class="ms-2">{{ $expenseType['name'] }}</span>
            </li>

            <li class="list-group-item d-flex align-items-center mb-3">
              <i class="bx bx-barcode me-2"></i>
              <strong>@lang('Code'):</strong> <span class="ms-2">{{ $expenseType['code'] }}</span>
            </li>

            <li class="list-group-item d-flex align-items-center mb-3">
              <i class="bx bx-dollar me-2"></i>
              <strong>@lang('Default Amount'):</strong> <span
                class="ms-2">{{$settings->currency_symbol}}{{ $expenseType['default_amount'] }}</span>
            </li>

            <li class="list-group-item d-flex align-items-center mb-3">
              <i class="bx bx-dollar-circle me-2"></i>
              <strong>@lang('Max Amount'):</strong> <span class="ms-2">{{ $expenseType['max_amount'] ?? 'N/A' }}</span>
            </li>

            <li class="list-group-item d-flex align-items-center mb-3">
              <i class="bx bx-check-circle me-2"></i>
              <strong>@lang('Is Proof Required'):</strong> <span
                class="ms-2">{{ $expenseType['is_proof_required'] ? 'Yes' : 'No' }}</span>
            </li>

            <li class="list-group-item d-flex align-items-center mb-3">
              <i class="bx bx-shield me-2"></i>
              <strong>@lang('Status'):</strong> <span class="ms-2">{{ $expenseType['status'] }}</span>
            </li>

            <li class="list-group-item d-flex align-items-center mb-3">
              <i class="bx bx-calendar me-2"></i>
              <strong>@lang('Created At'):</strong> <span class="ms-2">{{ $expenseType->createdAt() }}</span>
            </li>

            <li class="list-group-item d-flex align-items-center mb-3">
              <i class="bx bx-calendar-edit me-2"></i>
              <strong>@lang('Updated At'):</strong> <span class="ms-2">{{ $expenseType->updatedAt() }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Right Column: Expense Rules Table -->
    <div class="col-12 col-md-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5>@lang('Expense Rules')</h5>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRuleModal">
            @lang('Add Rule')
          </button>
        </div>
        <div class="card-body">
          <table class="table table-striped datatable">
            <thead>
            <tr>
              <th>@lang('Id')</th>
              <th>@lang('Designation')</th>
              <th>@lang('Location')</th>
              <th>@lang('Amount')</th>
              <th>@lang('Actions')</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($expenseRules as $rule)
              <tr>
                <td>{{ $rule->id }}</td>
                <td>{{ $rule->designation->name }}</td>
                <td>{{ $rule->location->name }}</td>
                <td>{{$settings->currency_symbol}}{{ $rule->amount }}</td>
                <td>
                  <div class="d-flex">
                    <button type="button" class="btn btn-sm btn-warning me-3 edit-rule"
                            data-rule="{{ json_encode($rule) }}">@lang('Edit')</button>
                    <button data-id="{{$rule->id}}"
                            class="btn btn-sm btn-danger delete-rule">@lang('Delete')</button>
                  </div>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Rule Modal -->
  <div class="modal fade" id="addRuleModal" tabindex="-1" aria-labelledby="addRuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addRuleModalLabel">@lang('Add Expense Rule')</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addRuleForm" method="POST" action="{{route('expenseTypes.addOrUpdateRule')}}">
            @csrf
            <input type="hidden" id="id" name="id" value="">
            <input type="hidden" id="expenseTypeId" name="expenseTypeId" value="{{$expenseType->id}}">
            <div class="mb-3">
              <label for="designation" class="form-label">@lang('Designation')</label>
              <select class="select2" id="designationId" name="designationId">
                <option value="">@lang('Select Designation')</option>
                @foreach ($designations as $designation)
                  <option value="{{ $designation->id }}">{{ $designation->getCodedName() }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label for="location" class="form-label">@lang('Location')</label>
              <select class="select2" id="locationId" name="locationId">
                <option value="">@lang('Select Location')</option>
                @foreach ($locations as $location)
                  <option value="{{ $location->id }}">{{ $location->getCodedName()}}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label for="amount" class="form-label">@lang('Amount')</label>
              <input type="number" class="form-control" id="amount" name="amount" placeholder="@lang('Enter amount')"
                     required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
          <button type="submit" id="submitBtn" class="btn btn-primary" form="addRuleForm">@lang('Save Rule')</button>
        </div>
      </div>
    </div>
  </div>
@endsection
