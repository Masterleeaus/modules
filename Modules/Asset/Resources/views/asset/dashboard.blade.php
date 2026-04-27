@extends('layouts.app')
@section('title', __('assetmanagement::lang.assets'))
@section('content')
    @includeIf('assetmanagement::layouts.nav')
    <!-- Main content -->
    <section class="content no-print">
        <div class="row">
            <div class="col-md-4">
                @component('components.static', [
                    'svg_bg' => 'tw-bg-cyan-400',
                    'svg_text' => 'tw-text-white',
                    'svg' => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-package-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21l-8 -4.5v-9l8 -4.5l8 4.5v4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12v9" /><path d="M12 12l-8 -4.5" /><path d="M22 18h-7" /><path d="M18 15l-3 3l3 3" /></svg>',
                ])
                    <p class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                        {{ __('assetmanagement::lang.total_assets_allocated_to_you') }}
                    </p>
                    <p class="tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                        {{ @num_format($total_assets_allocated) }}
                    </p>
                @endcomponent
            </div>

            <div class="col-md-4">
                <div class="card">
    <div class="card-body">
                    'class' => '',
                    'title' => __('assetmanagement::lang.expired_or_expiring_in_one_month'),
                ])
                    <table class="table">
                        <thead>
                            <tr>
                                <th>@lang('product.category')</th>
                                <th>@lang('assetmanagement::lang.total_assets_allocated_to_you')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($asset_allocation_by_category as $asset)
                                <tr>
                                    <td>{{ $asset->category }}</td>
                                    <td>{{ @num_format($asset->total_quantity_allocated) }}</td>
                                </tr>
                            @endforeach

                            @if (count($asset_allocation_by_category) == 0)
                                <tr>
                                    <td colspan="2" class="text-center">@lang('lang_v1.no_data')</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                
</div>
</div>
            </div>
        </div>

        @if ($is_admin)
            <hr>
            <div class="row">
                <div class="col-md-4">
                    @component('components.static', [
                        'svg_bg' => 'tw-bg-cyan-400',
                    'svg_text' => 'tw-text-white',
                        'svg' => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-package-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21l-8 -4.5v-9l8 -4.5l8 4.5v4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12v9" /><path d="M12 12l-8 -4.5" /><path d="M22 18h-7" /><path d="M18 15l-3 3l3 3" /></svg>',
                    ])
                        <p class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                            {{ __('assetmanagement::lang.total_assets') }}
                        </p>
                        <p
                            class="tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                            {{ @num_format($total_assets) }}
                        </p>
                    @endcomponent
                    @component('components.static', [
                    'svg_bg' => 'tw-bg-cyan-400',
                    'svg_text' => 'tw-text-white',
                        'svg' => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-package-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21l-8 -4.5v-9l8 -4.5l8 4.5v4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12v9" /><path d="M12 12l-8 -4.5" /><path d="M22 18h-7" /><path d="M18 15l-3 3l3 3" /></svg>',
                    ])
                        <p class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                            {{ __('assetmanagement::lang.total_assets_allocated') }}
                        </p>
                        <p
                            class="tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                            {{ @num_format($total_assets_allocated_for_all_users) }}
                        </p>
                    @endcomponent
                </div>

                <div class="col-md-4">
                    <div class="card">
    <div class="card-body">
                        'class' => '',
                        'title' => __('assetmanagement::lang.assets_by_category'),
                    ])
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>@lang('product.category')</th>
                                    <th>@lang('assetmanagement::lang.total_assets')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assets_by_category as $asset)
                                    <tr>
                                        <td>{{ $asset->category }}</td>
                                        <td>{{ @num_format($asset->total_quantity) }}</td>
                                    </tr>
                                @endforeach

                                @if (count($assets_by_category) == 0)
                                    <tr>
                                        <td colspan="2" class="text-center">@lang('lang_v1.no_data')</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    
</div>
</div>
                </div>

                <div class="col-md-4">
                    <div class="card">
    <div class="card-body">
                        'class' => '',
                        'title' => __('assetmanagement::lang.expired_or_expiring_in_one_month'),
                    ])
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>@lang('assetmanagement::lang.assets')</th>
                                    <th>@lang('assetmanagement::lang.warranty_status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expiring_assets as $asset)
                                    <tr>
                                        <td>{{ $asset->name }} - {{ $asset->asset_code }}</td>
                                        <td>
                                            @if (empty($asset->max_end_date))
                                                <span class="label bg-red">@lang('report.expired')</span>
                                            @else
                                                @if (\Carbon\Carbon::parse($asset->max_end_date)->lessThan(\Carbon\Carbon::today()))
                                                    <span class="label bg-red">@lang('report.expired'):
                                                        {{ @format_date($asset->max_end_date) }}</span>
                                                @else
                                                    <span class="label bg-yellow">@lang('assetmanagement::lang.expiring_on'):
                                                        {{ @format_date($asset->max_end_date) }}</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                @if (count($expiring_assets) == 0)
                                    <tr>
                                        <td colspan="2" class="text-center">@lang('lang_v1.no_data')</td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>
                    
</div>
</div>
                </div>


            </div>
        @endif
    </section>
@endsection
