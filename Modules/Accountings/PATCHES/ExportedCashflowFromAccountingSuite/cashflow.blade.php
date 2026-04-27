@extends('layouts.layoutMaster')

@section('title', __('Cash Flow Report'))

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/apex-charts/apexcharts.js'
  ])
@endsection

@section('content')
  <x-breadcrumb :title="__('Cash Flow Report')" :breadcrumbs="$breadcrumbs" />

  {{-- Date Range Filter --}}
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('accountingcore.reports.cashflow') }}" class="row g-3">
        <div class="col-md-4">
          <label class="form-label" for="start_date">{{ __('Start Date') }}</label>
          <input type="text" class="form-control date-picker" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label" for="end_date">{{ __('End Date') }}</label>
          <input type="text" class="form-control date-picker" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label d-block">&nbsp;</label>
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-filter me-1"></i> {{ __('Apply Filter') }}
          </button>
          <a href="{{ route('accountingcore.reports.cashflow.export-pdf', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="btn btn-label-success">
            <i class="bx bx-download me-1"></i> {{ __('Export PDF') }}
          </a>
        </div>
      </form>
    </div>
  </div>

  {{-- Balance Summary Cards --}}
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title text-muted mb-1">{{ __('Opening Balance') }}</h6>
              <h3 class="mb-0 {{ $openingBalance['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                {{ \App\Helpers\FormattingHelper::formatCurrency($openingBalance['balance']) }}
              </h3>
              <small class="text-muted">{{ __('As of') }} {{ $startDate->format('M d, Y') }}</small>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-info">
                <i class="bx bx-wallet bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title text-muted mb-1">{{ __('Net Change') }}</h6>
              @php
                $netChange = $closingBalance['balance'] - $openingBalance['balance'];
              @endphp
              <h3 class="mb-0 {{ $netChange >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $netChange >= 0 ? '+' : '' }}{{ \App\Helpers\FormattingHelper::formatCurrency($netChange) }}
              </h3>
              <small class="text-muted">{{ __('During period') }}</small>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-{{ $netChange >= 0 ? 'success' : 'danger' }}">
                <i class="bx bx-{{ $netChange >= 0 ? 'trending-up' : 'trending-down' }} bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="card-title text-muted mb-1">{{ __('Closing Balance') }}</h6>
              <h3 class="mb-0 {{ $closingBalance['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                {{ \App\Helpers\FormattingHelper::formatCurrency($closingBalance['balance']) }}
              </h3>
              <small class="text-muted">{{ __('As of') }} {{ $endDate->format('M d, Y') }}</small>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-primary">
                <i class="bx bx-wallet bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Cash Flow Chart --}}
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0">{{ __('Cash Flow Trend') }}</h5>
    </div>
    <div class="card-body">
      <div id="cashFlowChart"></div>
    </div>
  </div>

  {{-- Detailed Transaction Table --}}
  <div class="card">
    <div class="card-header">
      <h5 class="card-title mb-0">{{ __('Transaction Details') }}</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>{{ __('Date') }}</th>
              <th>{{ __('Description') }}</th>
              <th>{{ __('Category') }}</th>
              <th class="text-end">{{ __('Income') }}</th>
              <th class="text-end">{{ __('Expense') }}</th>
              <th class="text-end">{{ __('Balance') }}</th>
            </tr>
          </thead>
          <tbody>
            @if(count($cashflowData) > 0)
              @foreach($cashflowData as $item)
                <tr>
                  <td>{{ \App\Helpers\FormattingHelper::formatDate($item['date']) }}</td>
                  <td>{{ $item['transaction']->description }}</td>
                  <td>
                    <span class="badge bg-label-secondary">
                      {{ $item['transaction']->category ? $item['transaction']->category->name : __('Uncategorized') }}
                    </span>
                  </td>
                  <td class="text-end">
                    @if($item['transaction']->type === 'income')
                      <span class="text-success">{{ \App\Helpers\FormattingHelper::formatCurrency($item['transaction']->amount) }}</span>
                    @else
                      -
                    @endif
                  </td>
                  <td class="text-end">
                    @if($item['transaction']->type === 'expense')
                      <span class="text-danger">{{ \App\Helpers\FormattingHelper::formatCurrency($item['transaction']->amount) }}</span>
                    @else
                      -
                    @endif
                  </td>
                  <td class="text-end fw-semibold {{ $item['running_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ \App\Helpers\FormattingHelper::formatCurrency($item['running_balance']) }}
                  </td>
                </tr>
              @endforeach
            @else
              <tr>
                <td colspan="6" class="text-center text-muted py-4">
                  {{ __('No transactions found for the selected period') }}
                </td>
              </tr>
            @endif
          </tbody>
          @if(count($cashflowData) > 0)
            <tfoot class="table-light">
              <tr class="fw-bold">
                <td colspan="3">{{ __('Closing Balance') }}</td>
                <td class="text-end text-success">{{ \App\Helpers\FormattingHelper::formatCurrency($closingBalance['income']) }}</td>
                <td class="text-end text-danger">{{ \App\Helpers\FormattingHelper::formatCurrency($closingBalance['expense']) }}</td>
                <td class="text-end {{ $closingBalance['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                  {{ \App\Helpers\FormattingHelper::formatCurrency($closingBalance['balance']) }}
                </td>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>
  </div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers
    flatpickr('.date-picker', {
        dateFormat: 'Y-m-d',
        maxDate: 'today'
    });
    
    // Prepare chart data
    const cashflowData = @json($cashflowData);
    
    if (cashflowData.length > 0) {
        // Extract data for chart
        const dates = cashflowData.map(item => item.date);
        const balances = cashflowData.map(item => item.running_balance);
        
        // Cash Flow Chart
        const chartOptions = {
            series: [{
                name: '{{ __("Balance") }}',
                data: balances
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: true
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                type: 'datetime',
                categories: dates,
                labels: {
                    datetimeFormatter: {
                        year: 'yyyy',
                        month: 'MMM yyyy',
                        day: 'dd MMM',
                        hour: 'HH:mm'
                    }
                }
            },
            yaxis: {
                title: {
                    text: '{{ __("Balance") }}'
                },
                labels: {
                    formatter: function(value) {
                        return new Intl.NumberFormat('en-US', {
                            style: 'currency',
                            currency: 'USD',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(value);
                    }
                }
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy'
                },
                y: {
                    formatter: function(value) {
                        return new Intl.NumberFormat('en-US', {
                            style: 'currency',
                            currency: 'USD'
                        }).format(value);
                    }
                }
            },
            colors: ['#696cff'],
            grid: {
                borderColor: '#f1f1f1'
            }
        };
        
        const chart = new ApexCharts(document.querySelector("#cashFlowChart"), chartOptions);
        chart.render();
    } else {
        document.querySelector('#cashFlowChart').innerHTML = '<div class="text-center text-muted py-5">{{ __("No data to display") }}</div>';
    }
});
</script>
@endsection