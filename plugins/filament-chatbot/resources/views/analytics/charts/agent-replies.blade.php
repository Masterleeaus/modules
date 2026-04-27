@php
    $chart_data = $chartData ?? [];
    $months = $months ?? [];

    if (empty($chart_data)) {
        $chart_data = [
            [
                'name' => 'agent_replies',
                'data' => array_fill(0, 6, 0),
            ],
        ];
    }

    if (empty($months)) {
        $months = collect(range(0, 5))
            ->map(fn ($i) => now()->copy()->subMonths(5 - $i)->startOfMonth())
            ->all();
    }

    $currentMonthReplies = end($chart_data[0]['data']) ?: 0;
@endphp

<div x-data='chatbotAgentRepliesChart'>
    <x-card class:body="px-6">
        <x-slot:head
            class="flex items-center justify-between border-0 px-6 pt-6"
        >
            <div class="flex items-center gap-2">
                <h4 class="m-0 text-sm font-medium">
                    @lang('Agent Replies')
                </h4>
                <x-info-tooltip text="{{ __('Number of replies sent by human agents.') }}" />
            </div>

            <span class="text-2xs font-medium text-foreground/60">
                @lang('Monthly')
            </span>
        </x-slot:head>

        <p class="mb-6 text-2xs font-medium text-heading-foreground">
            <x-number-counter
                class="align-middle text-[24px] leading-none"
                id="chatbot-agent-replies-counter"
                value="{{ $currentMonthReplies }}"
            />
            @lang('This Month')
        </p>

        <div
            class="min-h-56"
            id="chatbot-agent-replies-chart"
        ></div>
    </x-card>
</div>

@push('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('chatbotAgentRepliesChart', () => ({
                chart: null,
                chartEl: document.querySelector('#chatbot-agent-replies-chart'),

                init() {
                    this.chart = new ApexCharts(this.chartEl, {
                        series: @json($chart_data),
                        colors: ['hsl(var(--primary))'],
                        chart: {
                            type: 'area',
                            height: 260,
                            toolbar: {
                                show: false
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        xaxis: {
                            categories: [
                                @foreach ($months as $month)
                                    '{{ ($month instanceof \Carbon\CarbonInterface ? $month : \Carbon\Carbon::parse($month))->locale(app()->getLocale())->shortMonthName }}',
                                @endforeach
                            ],
                            labels: {
                                offsetY: 0,
                                style: {
                                    colors: 'hsl(var(--foreground) / 40%)',
                                    fontSize: '11px',
                                    fontFamily: 'inherit',
                                    fontWeight: 500,
                                },
                            },
                            axisBorder: {
                                show: false,
                            },
                            axisTicks: {
                                show: false,
                            },
                        },
                        yaxis: {
                            labels: {
                                offsetX: -10,
                                style: {
                                    colors: 'hsl(var(--foreground) / 40%)',
                                    fontSize: '13px',
                                    fontFamily: 'inherit',
                                    fontWeight: 400,
                                },
                            },
                        },
                        stroke: {
                            width: 2,
                            curve: 'smooth',
                        },
                        grid: {
                            show: true,
                            borderColor: 'hsl(var(--border))',
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.3,
                                opacityTo: 0.05,
                                stops: [0, 100],
                            }
                        },
                        legend: {
                            show: false
                        }
                    });

                    this.chart.render();
                },
            }))
        });
    </script>
@endpush
