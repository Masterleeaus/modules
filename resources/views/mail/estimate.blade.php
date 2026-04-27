<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimate</title>
    <style>
        body { font-family: sans-serif; color: #1a1a1a; max-width: 600px; margin: 0 auto; padding: 24px; }
        .card { background: #f5f5f5; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .btn { display: inline-block; background: #2563eb; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; margin-top: 16px; }
        .footer { margin-top: 32px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <h1>Your Estimate is Ready</h1>

    <p>Hi {{ $estimate->customer->first_name }},</p>

    <p>Please review your estimate for <strong>{{ $estimate->title }}</strong>.</p>

    <div class="card">
        <p><strong>Estimate #{{ $estimate->estimate_number }}</strong></p>

        @if ($estimate->expires_at)
            <p>Valid until: {{ $estimate->expires_at->format('F j, Y') }}</p>
        @endif
    </div>

    <a href="{{ $url }}" class="btn">View &amp; Accept Estimate</a>

    <div class="footer">
        Thanks,<br>
        {{ config('app.name') }}
    </div>
</body>
</html>
