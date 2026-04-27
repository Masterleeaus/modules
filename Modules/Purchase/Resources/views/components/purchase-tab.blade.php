<a href="{{ $href }}" @if ($ajax == "false") {{ $attributes->merge(['class' => 'text-dark-grey  border-right-grey p-sub-menu']) }}

    @else
    {{ $attributes->merge(['class' => 'text-dark-grey  border-right-grey p-sub-menu ajax-tab']) }} @endif><span>{{ $text }}
    @if ($count != 0)
    <div class="badge badge-primary menu-item-count">{{ $count }}</div>
    @endif
</span>
