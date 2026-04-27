<!-- ROW START -->
<div class="row">
    <!--  USER CARDS START -->
    <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0">

        <x-cards.data :title="__('purchase::modules.inventory.inventoryInfo')">

            <div class="row">
                <div class="col-md-8">
                    <x-cards.data-row :label="__('app.date')" :value="\Carbon\Carbon::parse($inventory->date)->translatedFormat(company()->date_format) ??
                        '--'" />
                    <x-cards.data-row :label="__('purchase::modules.product.reason')" :value="$inventory->reason ? $inventory->reason->name : '--'" />
                    <x-cards.data-row :label="__('purchase::modules.product.modeOfAdjustment')" :value="($inventory->type ?? '--')" />

                </div>

                <div class="col-md-4">
                    @if (($editPermission == 'all' || $editPermission == 'added') || ($deletePermission == 'all' || $deletePermission == 'added'))
                        <x-slot name="action">
                            <div class="dropdown">
                                <button class="btn f-14 px-0 py-0 text-dark-grey dropdown-toggle" type="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                    aria-labelledby="dropdownMenuLink" tabindex="0">

                                    @if ($viewPermission == 'all' || $viewPermission == 'added')
                                        <a class="dropdown-item f-14 text-dark"
                                            href="{{ route('purchase_inventory.download', [$inventory->id]) }}">
                                            <i class="fa fa-download f-w-500 mr-2 f-11"></i> @lang('app.download')
                                        </a>

                                        <a class="dropdown-item" target="_blank" href="{{ route('purchase_inventory.download', [$inventory->id, 'view' => true]) }}">
                                            <i class="fa fa-eye mr-2"></i>
                                            @lang('app.viewPdf')
                                        </a>
                                    @endif

                                    @if ($deletePermission == 'all' || $deletePermission == 'added')
                                        <a class="dropdown-item delete-table-row" href="javascript:;"
                                            data-id="{{ $inventory->id }}"><i class="fa fa-trash f-w-500  mr-2 f-12"></i>
                                            @lang('app.delete')
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </x-slot>
                    @endif
                </div>

                <div class="col-md-12 table-responsive">
                    <table width="100%" class="table table-bordered">
                        <thead class="thead-light">
                            <tr height="55" class="bg-light-grey text-darkest-grey font-weight-bold">
                                <td class="f-15 pl-2">@lang('purchase::app.itemName')</td>
                                @if ($inventory->type == 'quantity')
                                    <td class="f-15 text-darkest-grey">@lang('purchase::modules.product.quantityOnHand')</td>
                                    <td class="f-15">@lang('purchase::modules.product.quabtityAdjusted')</td>
                                @else
                                    <td class="f-15">@lang('purchase::modules.product.changedValue')</td>
                                    <td class="f-15">@lang('purchase::modules.product.adjustedValue')</td>
                                @endif
                                <td class="f-15">@lang('app.description')</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($inventory->stocks as $item)
                                <tr height="55">
                                    <td class="pl-2">{{ $item->product ? ($item->product->name) : '--' }}</td>
                                    @if ($inventory->type == 'quantity')
                                        <td>{{ $item->net_quantity }}</td>
                                        <td>{{ $item->quantity_adjustment }}</td>
                                    @else
                                        <td>{{ $item->changed_value }}</td>
                                        <td>{{ $item->adjusted_value }}</td>
                                    @endif
                                    <td>{{ $item->description }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-cards.data>
    </div>
    <!--  USER CARDS END -->
</div>
<!-- ROW END -->
