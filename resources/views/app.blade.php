@php
    $brand = $platformBranding ?? [];
    $appTitle = $brand['meta_title'] ?? $brand['app_name'] ?? config('app.name', 'TITAN ZERO');
    $faviconUrl = $brand['favicon_url'] ?? null;
    $themeColor = $brand['primary_color'] ?? '#0f172a';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

        <title inertia>{{ $appTitle }}</title>
        @if (! empty($brand['meta_description']))
            <meta name="description" content="{{ $brand['meta_description'] }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Favicon -->
        @if ($faviconUrl)
            <link rel="icon" href="{{ $faviconUrl }}">
        @else
            <link rel="icon" type="image/svg+xml" href="/favicon.svg">
            <link rel="icon" type="image/x-icon" href="/favicon.ico">
        @endif

        <!-- PWA -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="{{ $themeColor }}">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="{{ $brand['app_name'] ?? 'TITAN ZERO' }}">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @if (! empty($brand['custom_css']))
            <style>{!! $brand['custom_css'] !!}</style>
        @endif

        <!-- Scripts -->
        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
