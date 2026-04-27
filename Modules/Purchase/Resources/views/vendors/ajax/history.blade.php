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
                            <x-date-badge :month="\Carbon\Carbon::parse($activity->created_at)->timezone(company()->timezone)->translatedFormat('M')" :date="\Carbon\Carbon::parse($activity->created_at)->timezone(company()->timezone)->translatedFormat('d')" />
                        </div>
                        <div class="card-body border-0 p-0 ml-3">
                               <h4 class="card-title f-14 font-weight-normal ">{!! __($activity->activity) !!}
                            </h4>
                            <div class="d-flex flex-grow-1">
                                <h4 class="card-title f-12 font-weight-normal text-dark mr-3 mb-1">
                                    @if($activity->details == "vendorCreated" || $activity->details == "vendorUpdated")
                                    <b>@lang('purchase::modules.vendor.' . $activity->label)</b><br>
                                    {{ __('purchase::modules.vendor.' . $activity->details) }} <b>{{ mb_ucwords($activity->user->name) }}</b> <a href="{{route('vendors.show', $activity->purchase_vendor_id)}}">View Details</a>
                                    @endif
                                    @if($activity->details == "vendorNoteCreated" || $activity->details == "vendorNoteUpdated")
                                    <b>@lang('purchase::modules.vendorPayment.' . $activity->label)</b><br>
                                    {{ __('purchase::modules.vendorPayment.' . $activity->details) }} <b>{{ mb_ucwords($activity->user->name) }}</b> <a href="{{route('vendor-notes.show', $activity->purchase_vendor_notes_id)}}">View Details</a>
                                    @endif
                                    @if($activity->details == "vendorContactCreated" || $activity->details == "vendorContactUpdated")
                                    <b>@lang('purchase::modules.vendorPayment.' . $activity->label)</b><br>
                                    {{ __('purchase::modules.vendorPayment.' . $activity->details) }} <b>{{ mb_ucwords($activity->user->name) }}</b> <a href="{{ route('vendors.show', $activity->purchase_vendor_id).'?tab=contacts' }}">View Details</a>
                                    @endif
                                    @if( $activity->details == "purchaseOrderCreated" || $activity->details == "purchaseOrderUpdated")
                                    <b>@lang('purchase::modules.purchaseOrder.' . $activity->label)</b><br>
                                    {{ __('purchase::modules.purchaseOrder.' . $activity->details) }} <b>{{ mb_ucwords($activity->user->name) }}</b> <a href="{{route('purchase-order.show', $activity->purchase_order_id)}}">View Details</a>
                                    @endif
                                    @if($activity->details == "vendorCreditCreated" || $activity->details == "vendorCreditUpdated")
                                    <b>@lang('purchase::modules.vendorCredit.' . $activity->label)</b><br>
                                    {{ __('purchase::modules.vendorCredit.' . $activity->details) }} <b>{{ mb_ucwords($activity->user->name) }}</b> {{ __('purchase::modules.vendorCredit.ofAmount') }} <b>{{ mb_ucwords($activity->amount) }}</b> <a href="{{route('vendor-credits.show', $activity->purchase_credit_id)}}">View Details</a>
                                    @endif
                                    @if( $activity->details == "billCreated")
                                    <b>@lang('purchase::modules.purchaseBill.' . $activity->label)</b><br>
                                    {{ __('purchase::modules.purchaseBill.' . $activity->details) }} <b>{{ $activity->amount }}</b> {{ __('purchase::modules.purchaseBill.ofPurchaseOrder') }} <b>{{$activity->purchase_order}}</b> {{ __('purchase::modules.purchaseBill.by') }} <b>{{ mb_ucwords($activity->user->name) }}</b> {{ __('purchase::modules.purchaseBill.onDate') }} <b>{{$activity->bill_date}}</b> <a href="{{route('bills.show', $activity->purchase_bill_id)}}">View Details</a>
                                    @endif
                                    @if ($activity->details == "billUpdated" )
                                    <b>@lang('purchase::modules.purchaseBill.' . $activity->label)</b><br>
                                    {{ __('purchase::modules.purchaseBill.' . $activity->details) }} <b>{{ $activity->bill_date }}</b><a href="{{route('bills.show', $activity->purchase_bill_id)}}"> View Details</a>
                                    @endif
                                    @if($activity->details == "vendorPaymentCreated" || $activity->details == "vendorPaymentUpdated" )
                                    <b>@lang('purchase::modules.vendorPayment.' . $activity->label)</b><br>
                                    {{ __('purchase::modules.vendorPayment.' . $activity->details) }} <b>{{$activity->amount}}</b> {{__('purchase::modules.vendorPayment.ofPurchaseOrder')}} <b>{{$activity->purchase_order}}</b> {{__('purchase::modules.vendorPayment.by')}} <b>{{ mb_ucwords($activity->user->name) }}</b>   <a href="{{route('vendor-payments.show', $activity->purchase_payment_id)}}">View Details</a>
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
