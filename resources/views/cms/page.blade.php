<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $content = is_array($page->website_content) ? $page->website_content : [];
        $logo = null;
        $favicon = null;
        $appName = 'Titan Zero';

        if ($settings) {
            $logo = $settings->logo_path ?? $settings->logo ?? null;
            $favicon = $settings->favicon_path ?? $settings->favicon ?? null;
            $appName = $settings->app_name ?? $settings->site_name ?? $appName;
        }
    @endphp

    <title>{{ $page->meta_title ?: $page->title }}</title>
    @if($page->meta_description)
        <meta name="description" content="{{ $page->meta_description }}">
    @endif
    @if($favicon)
        <link rel="icon" href="{{ asset('storage/'.$favicon) }}">
    @endif
    {{-- Standalone CMS renderer: avoids depending on the SPA Vite manifest entries. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    @if(! empty($settings?->primary_color))
        <style>
            :root { --tz-primary: {{ $settings->primary_color }}; }
            .bg-cyan-400, .bg-cyan-500 { background-color: var(--tz-primary) !important; }
            .text-cyan-200 { color: var(--tz-primary) !important; }
            .border-cyan-300 { border-color: var(--tz-primary) !important; }
        </style>
    @endif
</head>
<body class="bg-slate-950 text-white antialiased">
    <header class="sticky top-0 z-50 border-b border-white/10 bg-slate-950/90 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6">
            <a href="{{ url('/') }}" class="flex items-center gap-3 font-semibold tracking-tight">
                @if($logo)
                    <img src="{{ asset('storage/'.$logo) }}" alt="{{ $appName }}" class="h-9 max-w-[180px] object-contain">
                @else
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-500 font-bold text-slate-950">TZ</span>
                    <span>{{ $appName }}</span>
                @endif
            </a>
            <nav class="hidden items-center gap-6 text-sm text-slate-300 md:flex">
                <a href="/platform" class="hover:text-white">Platform</a>
                <a href="/apps" class="hover:text-white">Apps</a>
                <a href="/service-modes" class="hover:text-white">Service Modes</a>
                <a href="/pricing" class="hover:text-white">Pricing</a>
                <a href="/features" class="hover:text-white">Features</a>
                <a href="/faq" class="hover:text-white">FAQ</a>
                <a href="{{ route('login') }}" class="hover:text-white">Sign in</a>
                <a href="{{ route('register') }}" class="rounded-xl bg-cyan-400 px-4 py-2 font-semibold text-slate-950 hover:bg-cyan-300">Start free trial</a>
            </nav>
        </div>
    </header>

    <main>
        @forelse($content as $block)
            @php
                $type = (string) data_get($block, 'type', '');
                $data = data_get($block, 'data', $block);
            @endphp

            @if($type === 'HeroBlock')
                <section class="relative overflow-hidden bg-slate-950">
                    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_rgba(34,211,238,.22),_transparent_55%)]"></div>
                    <div class="relative mx-auto max-w-7xl px-6 py-24 text-center sm:py-32">
                        @if(data_get($data, 'eyebrow'))
                            <p class="mb-6 inline-flex rounded-full border border-cyan-300/30 bg-cyan-300/10 px-4 py-1.5 text-sm font-medium text-cyan-200">{{ data_get($data, 'eyebrow') }}</p>
                        @endif
                        <h1 class="mx-auto max-w-5xl text-5xl font-bold tracking-tight sm:text-7xl">{{ data_get($data, 'headline') }}</h1>
                        @if(data_get($data, 'subheadline'))
                            <p class="mx-auto mt-6 max-w-3xl text-lg text-slate-300 sm:text-xl">{{ data_get($data, 'subheadline') }}</p>
                        @endif
                        <div class="mt-10 flex flex-col justify-center gap-4 sm:flex-row">
                            @if(data_get($data, 'primary_button_label'))
                                <a href="{{ data_get($data, 'primary_button_url', '#') }}" class="rounded-xl bg-cyan-400 px-6 py-3 font-semibold text-slate-950 hover:bg-cyan-300">{{ data_get($data, 'primary_button_label') }}</a>
                            @endif
                            @if(data_get($data, 'secondary_button_label'))
                                <a href="{{ data_get($data, 'secondary_button_url', '#') }}" class="rounded-xl border border-white/15 px-6 py-3 font-semibold text-white hover:bg-white/10">{{ data_get($data, 'secondary_button_label') }}</a>
                            @endif
                        </div>
                        @if(data_get($data, 'note'))
                            <p class="mt-4 text-sm text-slate-500">{{ data_get($data, 'note') }}</p>
                        @endif
                    </div>
                </section>

            @elseif($type === 'AppGridBlock')
                <section id="apps" class="bg-slate-950 py-24">
                    <div class="mx-auto max-w-7xl px-6">
                        <div class="mx-auto max-w-3xl text-center">
                            <h2 class="text-4xl font-bold">{{ data_get($data, 'heading', 'Titan Apps') }}</h2>
                            @if(data_get($data, 'body'))
                                <p class="mt-4 text-slate-300">{{ data_get($data, 'body') }}</p>
                            @endif
                        </div>
                        <div class="mt-14 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                            @foreach((array) data_get($data, 'apps', []) as $app)
                                <a href="{{ data_get($app, 'url', '#') }}" class="group rounded-3xl border border-white/10 bg-white/[.04] p-6 transition hover:-translate-y-1 hover:border-cyan-300/60 hover:bg-cyan-300/10">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-200">{{ data_get($app, 'category', 'Titan App') }}</p>
                                    <h3 class="mt-3 text-2xl font-semibold text-white group-hover:text-cyan-100">{{ data_get($app, 'name') }}</h3>
                                    <p class="mt-3 text-sm text-slate-300">{{ data_get($app, 'description') }}</p>
                                    <div class="mt-5 flex flex-wrap gap-2">
                                        @foreach((array) data_get($app, 'capabilities', []) as $capability)
                                            <span class="rounded-full border border-white/10 bg-slate-950/50 px-2.5 py-1 text-xs text-slate-300">{{ $capability }}</span>
                                        @endforeach
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </section>

            @elseif($type === 'VerticalGridBlock')
                <section id="service-modes" class="bg-slate-900 py-24">
                    <div class="mx-auto max-w-7xl px-6">
                        <div class="mx-auto max-w-3xl text-center">
                            <h2 class="text-4xl font-bold">{{ data_get($data, 'heading', 'Service Modes') }}</h2>
                            @if(data_get($data, 'body'))
                                <p class="mt-4 text-slate-300">{{ data_get($data, 'body') }}</p>
                            @endif
                        </div>
                        <div class="mt-14 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                            @foreach((array) data_get($data, 'verticals', data_get($data, 'service_modes', [])) as $vertical)
                                <a href="{{ data_get($vertical, 'url', '#') }}" class="group rounded-3xl border border-white/10 bg-white/[.04] p-6 transition hover:-translate-y-1 hover:border-cyan-300/60 hover:bg-cyan-300/10">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-200">{{ data_get($vertical, 'tier', data_get($vertical, 'category', 'Service Mode')) }}</p>
                                    <h3 class="mt-3 text-xl font-semibold text-white group-hover:text-cyan-100">{{ data_get($vertical, 'title') }}</h3>
                                    <p class="mt-3 text-sm text-slate-300">{{ data_get($vertical, 'description') }}</p>
                                    <div class="mt-5 flex flex-wrap gap-2">
                                        @foreach((array) data_get($vertical, 'dashboards', data_get($vertical, 'apps', [])) as $dashboard)
                                            <span class="rounded-full border border-white/10 bg-slate-950/50 px-2.5 py-1 text-xs text-slate-300">{{ $dashboard }}</span>
                                        @endforeach
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </section>

            @elseif($type === 'FeatureGridBlock')
                <section id="features" class="bg-slate-900 py-24">
                    <div class="mx-auto max-w-7xl px-6">
                        <div class="mx-auto max-w-3xl text-center">
                            <h2 class="text-4xl font-bold">{{ data_get($data, 'heading', 'Features') }}</h2>
                            @if(data_get($data, 'body'))
                                <p class="mt-4 text-slate-300">{{ data_get($data, 'body') }}</p>
                            @endif
                        </div>
                        <div class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            @foreach((array) data_get($data, 'features', []) as $feature)
                                <article class="rounded-3xl border border-white/10 bg-white/[.04] p-6">
                                    <h3 class="text-xl font-semibold">{{ data_get($feature, 'title') }}</h3>
                                    <p class="mt-3 text-slate-300">{{ data_get($feature, 'description') }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>
            @elseif($type === 'PricingBlock')
                <section id="pricing" class="bg-slate-950 py-24">
                    <div class="mx-auto max-w-7xl px-6">
                        <div class="mx-auto max-w-3xl text-center">
                            <h2 class="text-4xl font-bold">{{ data_get($data, 'heading', 'Pricing') }}</h2>
                            @if(data_get($data, 'body'))
                                <p class="mt-4 text-slate-300">{{ data_get($data, 'body') }}</p>
                            @endif
                        </div>
                        <div class="mt-14 grid gap-6 lg:grid-cols-3">
                            @foreach((array) data_get($data, 'tiers', []) as $tier)
                                <article class="rounded-3xl border p-8 {{ data_get($tier, 'highlight') ? 'border-cyan-300 bg-cyan-300/10' : 'border-white/10 bg-white/[.04]' }}">
                                    <h3 class="text-2xl font-semibold">{{ data_get($tier, 'name') }}</h3>
                                    <p class="mt-4 text-4xl font-bold">{{ data_get($tier, 'price') }}</p>
                                    <p class="mt-3 text-slate-300">{{ data_get($tier, 'description') }}</p>
                                    <ul class="mt-6 space-y-2 text-sm text-slate-200">
                                        @foreach((array) data_get($tier, 'features', []) as $feature)
                                            <li>✓ {{ $feature }}</li>
                                        @endforeach
                                    </ul>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>
            @elseif($type === 'FaqBlock')
                <section id="faq" class="bg-slate-900 py-24">
                    <div class="mx-auto max-w-4xl px-6">
                        <h2 class="text-center text-4xl font-bold">{{ data_get($data, 'heading', 'FAQ') }}</h2>
                        <div class="mt-12 space-y-4">
                            @foreach((array) data_get($data, 'items', []) as $item)
                                <article class="rounded-2xl border border-white/10 bg-white/[.04] p-6">
                                    <h3 class="font-semibold">{{ data_get($item, 'question') }}</h3>
                                    <p class="mt-2 text-slate-300">{{ data_get($item, 'answer') }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>
            @elseif($type === 'CtaBlock')
                <section class="bg-slate-950 py-24">
                    <div class="mx-auto max-w-5xl rounded-3xl border border-cyan-300/20 bg-cyan-300/10 px-6 py-16 text-center">
                        <h2 class="text-4xl font-bold">{{ data_get($data, 'heading') }}</h2>
                        @if(data_get($data, 'body'))
                            <p class="mx-auto mt-4 max-w-2xl text-slate-300">{{ data_get($data, 'body') }}</p>
                        @endif
                        @if(data_get($data, 'button_label'))
                            <a href="{{ data_get($data, 'button_url', '#') }}" class="mt-8 inline-flex rounded-xl bg-cyan-400 px-6 py-3 font-semibold text-slate-950 hover:bg-cyan-300">{{ data_get($data, 'button_label') }}</a>
                        @endif
                    </div>
                </section>
            @else
                <section class="mx-auto max-w-6xl px-6 py-16">
                    <div class="rounded-3xl border border-white/10 bg-white/[.04] p-8">
                        @if(data_get($data, 'heading'))
                            <h2 class="text-3xl font-semibold">{{ data_get($data, 'heading') }}</h2>
                        @endif
                        @if(data_get($data, 'body'))
                            <div class="prose prose-invert mt-4 max-w-none">{!! data_get($data, 'body') !!}</div>
                        @endif
                    </div>
                </section>
            @endif
        @empty
            <section class="mx-auto max-w-5xl px-6 py-24 text-center">
                <h1 class="text-5xl font-bold">{{ $page->title }}</h1>
                @if($page->summary)
                    <p class="mt-6 text-slate-300">{{ $page->summary }}</p>
                @endif
                <p class="mt-10 rounded-2xl border border-white/10 bg-white/[.04] p-6 text-slate-300">This CMS page is published, but no blocks have been added yet.</p>
            </section>
        @endforelse
    </main>

    <footer class="border-t border-white/10 bg-slate-950 py-10 text-center text-sm text-slate-400">
        {{ $settings->footer_text ?? '© '.date('Y').' '.$appName.'. All rights reserved.' }}
    </footer>
</body>
</html>
