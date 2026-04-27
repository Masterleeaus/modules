<!-- ROW START -->
<div class="row">
    <!--  USER CARDS START -->
    <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0">

        <x-cards.data :title="__('purchase::modules.product.productInfo')">

            <div class="row">
                <div class="col-md-8">
                    <x-cards.data-row :label="__('app.name')" :value="$product->name ?? '--'" />
                    <x-cards.data-row :label="__('purchase::modules.product.productType')" :value="($product->type ?? '--')" />
                    <x-cards.data-row :label="__('modules.unitType.unitType')" :value="($product->unit->unit_type ?? '--')" />
                    <x-cards.data-row :label="__('modules.invoices.tax')" :value="!empty($taxValue) ? strtoupper($taxValue) : '--'" />
                    <x-cards.data-row :label="__('app.hsnSac')" :value="$product->hsn_sac_code ?? '--'" />
                    <x-cards.data-row :label="__('modules.productCategory.productCategory')"
                        :value="$product->category->category_name ?? '--'" />
                    <x-cards.data-row :label="__('modules.productCategory.productSubCategory')"
                        :value="$product->subCategory->category_name ?? '--'" />

                    @if (!in_array('client', user_roles()))
                        <x-cards.data-row :label="__('app.purchaseAllow')" :value="($product->allow_purchase) ? '<span class=\'badge badge-success\'>'.
                            __('app.yes').' </span>': '<span class=\'badge badge-danger\'>'.
                                __('app.no').' </span>'" />
                    @endif
                    <x-cards.data-row :label="__('app.downloadable')" :value="($product->downloadable) ? '<span class=\'badge badge-success\'>'.
                        __('app.yes').' </span>': '<span class=\'badge badge-danger\'>'.
                            __('app.no').' </span>'" />
                    @if ($product->downloadable && !in_array('client', user_roles()))
                        <x-cards.data-row :label="__('app.downloadableFile')"
                            :value="'<a href='.$product->download_file_url.' download><span class=\'badge badge-success\'>'.__('app.view').'</span></a>'" />

                    @endif
                    <x-cards.data-row :label="__('app.description')" :value="!empty($product->description) ? $product->description : '--'"
                        html="true" />

                    <p class="f-w-500">{{ __('purchase::app.salesInformation') }}</p>
                    <x-cards.data-row :label="__('purchase::app.sellingPrice')" :value="$product->price ? currency_format($product->price) : '--'" />

                    <p class="f-w-500">{{ __('purchase::app.purchaseInformation') }}</p>
                    <x-cards.data-row :label="__('purchase::app.costPrice')" :value="$product->purchase_price ? currency_format($product->purchase_price) : '--'" />

                    <!-- Custom fields data -->
                    <x-forms.custom-field-show :fields="$fields" :model="$productData"></x-forms.custom-field-show>
                </div>

                <div class="col-md-4">
                    <x-slot name="action">
                        <div class="dropdown">
                            <button class="btn f-14 px-0 py-0 text-dark-grey dropdown-toggle" type="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-h"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                aria-labelledby="dropdownMenuLink" tabindex="0">
                                @if (($product->type == 'goods') && $editInventoryPermission == 'all' || $editInventoryPermission == 'added')
                                    <a class="dropdown-item" id="stock-adjustment"
                                        >@lang('purchase::app.adjustStock')</a>
                                @endif
                                <a class="dropdown-item openRightModal"
                                    href="{{ route('purchase-products.edit', $product->id) }}">@lang('app.edit')</a>
                                @if ($deletePermission == 'all' || ($deletePermission == 'added' && $products->added_by == user()->id))
                                    <a class="dropdown-item delete-table-row" href="javascript:;" data-id="{{ $product->id }}">
                                            @lang('app.delete')
                                        </a>
                                @endif
                            </div>
                        </div>
                    </x-slot>

                    @if ($product->image_url)
                            <a href="javascript:;" class="img-lightbox" data-image-url="{{ $product->image_url }}">
                                <img src="{{ $product->image_url }}" width="100" height="100" class="img-thumbnail">
                            </a>
                    @endif
                    <div class="table-responsive">
                        <x-table class="table-bordered">
                            <tbody>
                                <tr>
                                    <x-cards.data-row :label="__('purchase::modules.product.openingStock')" :value="$product->opening_stock ? '<strong>&nbsp'.$product->opening_stock.'</strong>' : '&nbsp--'" />
                                </tr>
                                <tr>
                                    <x-cards.data-row :label="__('purchase::modules.product.stockOnHand')" :value="$inventory ? '<strong>&nbsp'.$inventory->net_quantity.'</strong>' : '&nbsp--'" />
                                </tr>
                                <tr>
                                    <x-cards.data-row :label="__('purchase::modules.product.committedStock')" :value="$commitedStock ? '<strong>&nbsp'.$commitedStock.'</strong>' : '&nbsp--'" />
                                </tr>
                                <tr>
                                    <x-cards.data-row :label="__('purchase::modules.product.availableForSale')" :value="$inventory ? '<strong>&nbsp'.$inventory->net_quantity - $commitedStock.'</strong>' : '&nbsp--'" />
                                </tr>
                            </tbody>
                        </x-table>
                    </div>
                </div>
            </div>
        </x-cards.data>
    </div>
    <!--  USER CARDS END -->
</div>
<!-- ROW END -->
