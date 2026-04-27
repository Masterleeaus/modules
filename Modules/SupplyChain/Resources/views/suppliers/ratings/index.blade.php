<x-filament-panels::page>
    <div class="space-y-2">
        @foreach($suppliers as $supplier)
            <div>{{ $supplier->name }} — {{ $supplier->fsm_rating ?? '-' }}</div>
        @endforeach
    </div>
</x-filament-panels::page>
