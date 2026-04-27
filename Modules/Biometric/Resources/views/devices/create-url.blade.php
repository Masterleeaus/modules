@props([
    'serverAddress' => preg_replace('/^https?:\/\//', '', config('app.url'))
])

<div id="css-cloud-panel">
    <div class="css-title">Cloud Server Setting</div>

    <div class="css-row css-selected">
        <span class="css-label">Server Mode</span>
        <span class="css-value">ADMS</span>
    </div>

    <div class="css-row">
        <span class="css-label">Enable Domain Name</span>
        <span class="css-value">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="32" viewBox="0 0 64 32">
                <rect width="64" height="32" rx="4" fill="#30B0E6"/>
                <rect x="34" y="2" width="28" height="28" rx="4" fill="#F4F4F4"/>
                <text x="12" y="22" font-family="Arial" font-size="16" fill="#FFFFFF">ON</text>
                </svg>
        </span>
    </div>

    <div class="css-row">
        <span class="css-label">Server Address</span>
        <input type="text" name="server_address"
               value="{{ $serverAddress }}"
               class="css-value-input">
    </div>

    <div class="css-row">
        <span class="css-label">Enable Proxy Server</span>
        <span class="css-value"><svg xmlns="http://www.w3.org/2000/svg" width="64" height="32" viewBox="0 0 64 32">
            <rect width="64" height="32" rx="4" fill="#c0c0c0"/>
            <rect x="2" y="2" width="28" height="28" rx="4" fill="#F4F4F4"/>
            <text x="38" y="22" font-family="Arial" font-size="16" fill="#666666">OFF</text>
            </svg></span>
    </div>

    <div class="css-row">
        <span class="css-label">HTTPS</span>
        <span class="css-value"><svg xmlns="http://www.w3.org/2000/svg" width="64" height="32" viewBox="0 0 64 32">
            <rect width="64" height="32" rx="4" fill="#30B0E6"/>
            <rect x="34" y="2" width="28" height="28" rx="4" fill="#F4F4F4"/>
            <text x="12" y="22" font-family="Arial" font-size="16" fill="#FFFFFF">ON</text>
            </svg></span>
    </div>
</div>

@push('styles')
<style>
/* prefix every rule with #css-cloud-panel … */
#css-cloud-panel{
    width:40rem;background:#000;color:#fff;border-radius:4px;overflow:hidden;
    box-shadow:0 0 10px rgba(0,0,0,.7);font-family:Arial,system-ui,sans-serif
}
#css-cloud-panel .css-title{background:#2d2d2d;text-align:center;padding:12px;font-weight:700;font-size:1.25rem}
#css-cloud-panel .css-row{display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-top:1px solid #333;font-size:1rem}
#css-cloud-panel .css-selected{background:#f5c843;color:#000;font-weight:700}
#css-cloud-panel .css-label{flex:1}
#css-cloud-panel .css-value{display:flex;align-items:center}
#css-cloud-panel .css-value-input{flex:0 0 18rem;background:transparent;border:none;color:#fff;font-size:1rem;text-align:right;outline:none}
#css-cloud-panel .css-row:not(.css-selected):hover{background:#111}
#css-cloud-panel .css-toggle{width:4rem;height:2rem}
</style>
@endpush
