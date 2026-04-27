@extends('layouts/layoutMaster')

@section('title', __('Compensatory Off Requests'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
  ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/apex-charts/apexcharts.js'
  ])
@endsection

@section('page-script')
  @vite(['Modules/HRCore/resources/assets/js/compensatory-off.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Compensatory Off Requests')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Compensatory Off'), 'url' => '']
      ]"
      :home-url="url('/')"
    >
      @can('hrcore.create-comp-off')
        <a href="{{ route('hrcore.compensatory-offs.create') }}" class="btn btn-primary">
          <i class="bx bx-plus me-1"></i>{{ __('Add Compensatory Off') }}
        </a>
      @endcan
    </x-breadcrumb>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
      <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0">
              <h5 class="m-0 me-2">{{ __('Total Earned') }}</h5>
              <small class="text-muted">{{ __('All Time') }}</small>
            </div>
            <div class="dropdown">
              <span class="badge bg-label-primary rounded-pill">{{ $statistics['total_earned'] ?? 0 }}</span>
            </div>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <div class="avatar">
                  <div class="avatar-initial bg-primary rounded">
                    <i class="bx bx-time-five"></i>
                  </div>
                </div>
                <div class="ms-3">
                  <div class="small text-muted">{{ __('Days') }}</div>
                  <h6 class="mb-0">{{ $statistics['total_earned'] ?? 0 }}</h6>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0">
              <h5 class="m-0 me-2">{{ __('Available') }}</h5>
              <small class="text-muted">{{ __('Can Use') }}</small>
            </div>
            <div class="dropdown">
              <span class="badge bg-label-success rounded-pill">{{ $statistics['available'] ?? 0 }}</span>
            </div>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <div class="avatar">
                  <div class="avatar-initial bg-success rounded">
                    <i class="bx bx-check-circle"></i>
                  </div>
                </div>
                <div class="ms-3">
                  <div class="small text-muted">{{ __('Days') }}</div>
                  <h6 class="mb-0">{{ $statistics['available'] ?? 0 }}</h6>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0">
              <h5 class="m-0 me-2">{{ __('Used') }}</h5>
              <small class="text-muted">{{ __('Utilized') }}</small>
            </div>
            <div class="dropdown">
              <span class="badge bg-label-info rounded-pill">{{ $statistics['used'] ?? 0 }}</span>
            </div>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <div class="avatar">
                  <div class="avatar-initial bg-info rounded">
                    <i class="bx bx-calendar-minus"></i>
                  </div>
                </div>
                <div class="ms-3">
                  <div class="small text-muted">{{ __('Days') }}</div>
                  <h6 class="mb-0">{{ $statistics['used'] ?? 0 }}</h6>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0">
              <h5 class="m-0 me-2">{{ __('Expired') }}</h5>
              <small class="text-muted">{{ __('Lost') }}</small>
            </div>
            <div class="dropdown">
              <span class="badge bg-label-danger rounded-pill">{{ $statistics['expired'] ?? 0 }}</span>
            </div>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <div class="avatar">
                  <div class="avatar-initial bg-danger rounded">
                    <i class="bx bx-x-circle"></i>
                  </div>
                </div>
                <div class="ms-3">
                  <div class="small text-muted">{{ __('Days') }}</div>
                  <h6 class="mb-0">{{ $statistics['expired'] ?? 0 }}</h6>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Monthly Earning Chart --}}
    @if(!empty($statistics['by_month']))
    <div class="row mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">{{ __('Monthly Compensatory Off Earnings') }}</h5>
            <small class="text-muted">{{ __('Current Year') }}</small>
          </div>
          <div class="card-body">
            <div id="compOffChart"></div>
          </div>
        </div>
      </div>
    </div>
    @endif

    {{-- Filters Card --}}
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">{{ __('Filters') }}</h5>
        <div class="row g-3">
          {{-- Employee Filter --}}
          <div class="col-md-3">
            <label for="employeeFilter" class="form-label">{{ __('Filter by Employee') }}</label>
            <select id="employeeFilter" name="employeeFilter" class="form-select">
              <option value="" selected>{{ __('All Employees') }}</option>
              @foreach($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->code }})</option>
              @endforeach
            </select>
          </div>

          {{-- Status Filter --}}
          <div class="col-md-3">
            <label for="statusFilter" class="form-label">{{ __('Filter by Status') }}</label>
            <select id="statusFilter" name="statusFilter" class="form-select">
              <option value="" selected>{{ __('All Statuses') }}</option>
              <option value="pending">{{ __('Pending') }}</option>
              <option value="approved">{{ __('Approved') }}</option>
              <option value="rejected">{{ __('Rejected') }}</option>
            </select>
          </div>

          {{-- Usage Status Filter --}}
          <div class="col-md-3">
            <label for="usageFilter" class="form-label">{{ __('Usage Status') }}</label>
            <select id="usageFilter" name="usageFilter" class="form-select">
              <option value="" selected>{{ __('All') }}</option>
              <option value="available">{{ __('Available') }}</option>
              <option value="used">{{ __('Used') }}</option>
              <option value="expired">{{ __('Expired') }}</option>
            </select>
          </div>

          {{-- Date Range Filter --}}
          <div class="col-md-3">
            <label for="dateRangeFilter" class="form-label">{{ __('Worked Date Range') }}</label>
            <input type="text" id="dateRangeFilter" name="dateRangeFilter" class="form-control" placeholder="{{ __('Select date range') }}">
          </div>
        </div>
      </div>
    </div>

    {{-- Compensatory Off Table --}}
    <div class="card">
      <div class="card-datatable table-responsive">
        <table id="compOffTable" class="table">
          <thead>
            <tr>
              <th>{{ __('ID') }}</th>
              <th>{{ __('Employee') }}</th>
              <th>{{ __('Worked Date') }}</th>
              <th>{{ __('Hours Worked') }}</th>
              <th>{{ __('Comp Off Days') }}</th>
              <th>{{ __('Expiry Date') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('Usage Status') }}</th>
              <th>{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    {{-- Page Data for JavaScript --}}
    <script>
      const pageData = {
        urls: {
          datatable: @json(route('hrcore.compensatory-offs.datatable')),
          show: @json(route('hrcore.compensatory-offs.show', ':id')),
          approve: @json(route('hrcore.compensatory-offs.approve', ':id')),
          reject: @json(route('hrcore.compensatory-offs.reject', ':id')),
          destroy: @json(route('hrcore.compensatory-offs.destroy', ':id')),
          statistics: @json(route('hrcore.compensatory-offs.statistics'))
        },
        labels: {
          search: @json(__('Search')),
          processing: @json(__('Processing...')),
          lengthMenu: @json(__('Show _MENU_ entries')),
          info: @json(__('Showing _START_ to _END_ of _TOTAL_ entries')),
          infoEmpty: @json(__('Showing 0 to 0 of 0 entries')),
          emptyTable: @json(__('No data available')),
          paginate: {
            first: @json(__('First')),
            last: @json(__('Last')),
            next: @json(__('Next')),
            previous: @json(__('Previous'))
          },
          selectEmployee: @json(__('Select Employee')),
          viewDetails: @json(__('View Details')),
          error: @json(__('An error occurred. Please try again.')),
          success: @json(__('Success')),
          confirmAction: @json(__('Are you sure?')),
          approved: @json(__('Approved')),
          rejected: @json(__('Rejected')),
          pending: @json(__('Pending')),
          approve: @json(__('Approve')),
          reject: @json(__('Reject')),
          delete: @json(__('Delete')),
          notes: @json(__('Notes')),
          reason: @json(__('Reason')),
          enterNotes: @json(__('Enter notes (optional)')),
          enterReason: @json(__('Enter reason for rejection'))
        },
        statistics: @json($statistics ?? [])
      };
    </script>

    {{-- Chart Script --}}
    @if(!empty($statistics['by_month']))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($statistics['by_month']);
        
        const options = {
          series: [{
            name: '{{ __("Days Earned") }}',
            data: chartData.map(item => item.earned)
          }],
          chart: {
            type: 'bar',
            height: 300,
            toolbar: {
              show: false
            }
          },
          colors: ['#7367f0'],
          plotOptions: {
            bar: {
              borderRadius: 4,
              horizontal: false,
            },
          },
          dataLabels: {
            enabled: false
          },
          xaxis: {
            categories: chartData.map(item => item.month)
          },
          yaxis: {
            title: {
              text: '{{ __("Days") }}'
            }
          },
          grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 5
          }
        };

        const chart = new ApexCharts(document.querySelector("#compOffChart"), options);
        chart.render();
      });
    </script>
    @endif
@endsection