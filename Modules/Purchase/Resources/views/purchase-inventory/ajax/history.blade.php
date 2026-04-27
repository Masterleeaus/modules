<!-- ROW START -->
<div class="row py-0 py-md-0 py-lg-3">
    <div class="col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4">
        <!-- ACTIVITY DETAIL START -->
        <div class="p-activity-detail cal-info b-shadow-4" data-menu-vertical="1" data-menu-scroll="1"
            data-menu-dropdown-timeout="500" id="projectActivityDetail">
            @forelse($history as $key=>$activity)
                <div class="card border-0 b-shadow-4 p-20 rounded">
                    <div class="card-horizontal">
                        <div class="card-header mt-3 p-0 bg-white rounded">
                            <x-date-badge :month="$activity->created_at->timezone(company()->timezone)->translatedFormat('M')" :date="$activity->created_at->timezone(company()->timezone)->translatedFormat('d')" />
                        </div>
                        <div class="card-body border-0 p-0 ml-3">
                               <h4 class="card-title f-14 font-weight-normal ">{!! __($activity->activity) !!}
                            </h4>
                            <div class="d-flex flex-grow-1">
                                <h4 class="card-title f-12 font-weight-normal text-dark mr-3 mb-1">
                                    @if($activity->inventory_id && $activity->net_quantity == null && $activity->quantity_adjustment == null && $activity->changed_value == null && $activity->adjusted_value == null && $activity->purchase_inventory_files_id == null)
                                    <b>@lang('purchase::modules.inventory.' . $activity->label)</b><br>
                                    {{ __('purchase::modules.inventory.details') }} <b>{{ mb_ucwords($activity->user->name) }}</b> <a href="{{route('purchase-inventory.show', $activity->inventory_id)}}">View Details</a>
                                    @endif
                                    @if($activity->inventory_id && $activity->net_quantity != null)
                                    <b>@lang('purchase::modules.inventory.' . $activity->label)</b><br>
                                    {{ __('purchase::modules.inventory.' . $activity->details) }} <b>{{$activity->product_name}}</b> {{__('purchase::modules.inventory.withQuantityOnHand')}} <b>{{ $activity->net_quantity }}</b> {{__('purchase::modules.vendorPayment.by')}} <b>{{ mb_ucwords($activity->user->name) }}</b> <a href="{{route('purchase-inventory.show', $activity->inventory_id)}}">View Details</a>
                                    @endif
                                    @if($activity->inventory_id && $activity->changed_value != null && $activity->adjusted_value != null )
                                    <b>@lang('purchase::modules.inventory.' . $activity->label)</b><br>
                                    {{ __('purchase::modules.inventory.' . $activity->details) }} <b>{{$activity->product_name}}</b> {{ __('purchase::modules.inventory.toNewValue') }} <b>{{$activity->changed_value}}</b> {{__('purchase::modules.vendorPayment.by')}} <b>{{ mb_ucwords($activity->user->name) }}</b> <a href="{{route('purchase-inventory.show', $activity->inventory_id)}}">View Details</a>
                                    @endif
                                    @if($activity->purchase_inventory_files_id != null)
                                    <b>@lang('purchase::modules.inventory.' . $activity->label)</b><br>
                                    {{ __('purchase::modules.inventory.' . $activity->details) }} <b>{{ mb_ucwords($activity->user->name) }}</b> <a href="{{route('purchase-inventory.show', $activity->inventory_id)}}">View Details</a>
                                    @endif
                                </h4>
                            </div>
                            <p class="card-text f-12 text-dark-grey">
                               {{ ($activity->created_at->diffForHumans()) }}
                            </p>

                        </div>
                    </div>
                </div><!-- card end -->
            @empty
                <div class="card border-0 p-20 rounded">
                    <div class="card-horizontal">

                        <div class="card-body border-0 p-0 ml-3">
                            <h4 class="card-title f-14 font-weight-normal">
                                @lang('messages.noActivityByThisUser')</h4>
                            <p class="card-text f-12 text-dark-grey"></p>
                        </div>
                    </div>
                </div><!-- card end -->
            @endforelse


        </div>
        <!-- ACTIVITY DETAIL END -->
    </div>
</div>
