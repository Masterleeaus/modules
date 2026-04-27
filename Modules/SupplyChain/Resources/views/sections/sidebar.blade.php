{{-- Inventory module sidebar --}}
@php($user = auth()->user())
@if ($user && $user->can('supplychain.view'))
    <li class="sidebar-item">
        <a class="sidebar-link has-arrow" href="#" aria-expanded="false">
            <i class="fa fa-archive sidebar-icon"></i>
            <span>{{ __('supplychain::labels.menu') }}</span>
        </a>
        <ul class="collapse first-level" aria-expanded="false">
            <li class="sidebar-item">
                <a href="{{ route('supplychain.index') }}" class="sidebar-link">
                    <i class="fa fa-tachometer sidebar-icon"></i>
                    <span>{{ __('supplychain::labels.dashboard') }}</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('supplychain.warehouses.index') }}" class="sidebar-link">
                    <i class="fa fa-building sidebar-icon"></i>
                    <span>{{ __('supplychain::labels.warehouses') }}</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('supplychain.items.index') }}" class="sidebar-link">
                    <i class="fa fa-cubes sidebar-icon"></i>
                    <span>{{ __('supplychain::labels.stock') }}</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('supplychain.movements.index') }}" class="sidebar-link">
                    <i class="fa fa-exchange sidebar-icon"></i>
                    <span>{{ __('supplychain::labels.movements') }}</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('supplychain.transfers.index') }}" class="sidebar-link">
                    <i class="fa fa-truck sidebar-icon"></i>
                    <span>{{ __('supplychain::labels.transfers') }}</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('supplychain.purchasing.index') }}" class="sidebar-link">
                    <i class="fa fa-shopping-cart sidebar-icon"></i>
                    <span>{{ __('supplychain::labels.purchase_orders') }}</span>
                </a>
            </li>
            @if ($user->can('supplychain.suppliers.view'))
            <li class="sidebar-item">
                <a href="{{ route('supplychain.suppliers.index') }}" class="sidebar-link">
                    <i class="fa fa-handshake-o sidebar-icon"></i>
                    <span>{{ __('supplychain::labels.suppliers') }}</span>
                </a>
            </li>
            @endif
        </ul>
    </li>
@endif
