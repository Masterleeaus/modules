@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')

    <div class="content-wrapper">

        @php
            $addProductPermission = user()->permission('add_product');
        @endphp

        <div class="bg-white rounded b-shadow-4 create-inv">
            <!-- HEADING START -->
            <div class="px-lg-4 px-md-4 px-3 py-3">
                <h4 class="mb-0 f-21 font-weight-normal text-capitalize">@lang('accountings::app.acc.editJournal')</h4>
            </div>
            <!-- HEADING END -->
            <hr class="m-0 border-top-grey">
            <!-- FORM START -->
            <x-form class="c-inv-form" id="saveInvoiceForm">
                @method('PUT')
                <div class="row px-lg-4 px-md-4 px-3 py-3">
                    <div class="col-md-2">
                        <div class="form-group mb-lg-0 mb-md-0 mb-4">
                            <x-forms.label class="mb-12" fieldId="invoice_number" :fieldLabel="__('accountings::app.menu.journalNo')" fieldRequired="true">
                            </x-forms.label>
                            <x-forms.input-group>
                                <input type="text" name="invoice_number" id="invoice_number"
                                    class="form-control height-35 f-15" value="{{ $invoice->no_journal }}">
                            </x-forms.input-group>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="form-group mb-lg-0 mb-md-0 mb-4">
                            <x-forms.label fieldId="due_date" :fieldLabel="__('accountings::app.menu.journalDate')" fieldRequired="true">
                            </x-forms.label>
                            <div class="input-group">
                                <input type="text" id="invoice_date" name="issue_date"
                                    class="px-6 position-relative text-dark font-weight-normal form-control height-35 rounded p-0 text-left f-15"
                                    placeholder="@lang('placeholders.date')"
                                    value="{{ \Carbon\Carbon::createFromFormat('Y-m-d', $invoice->journal_date)->format('d-m-Y') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="form-group mb-lg-0 mb-md-0 mb-4">
                            <x-forms.label fieldId="parent_label" :fieldLabel="__('accountings::app.menu.journalType')" fieldName="parent_label"
                                fieldRequired="true">
                            </x-forms.label>
                            <x-forms.input-group>
                                <select class="form-control select-picker" name="typejournal_id" id="typejournal_id">
                                    @foreach ($jt as $items)
                                        <option @if ($invoice->typejournal_id == $items->id) selected @endif
                                            value="{{ $items->id }}">
                                            {{ $items->type_journal }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-slot name="append">
                                    <button id="add-type" type="button" data-toggle="tooltip"
                                        data-original-title="{{ __('app.add') . ' ' . __('modules.invoices.tax') }}"
                                        class="btn btn-outline-secondary border-grey">@lang('app.add')</button>
                                </x-slot>
                            </x-forms.input-group>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="reff_journal" :fieldLabel="__('accountings::app.menu.journalReff')" fieldName="reff_journal" fieldRequired="true"
                            :fieldValue="$invoice->reff_journal">
                        </x-forms.text>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="remark" :fieldLabel="__('accountings::app.menu.remark')" fieldName="remark" fieldRequired="true"
                            :fieldValue="$invoice->remark">
                        </x-forms.text>
                    </div>
                </div>
                <!-- CLIENT, PROJECT, GST, BILLING, SHIPPING ADDRESS END -->
                <hr class="m-0 border-top-grey">

                <div id="sortable">
                    @if (isset($invoice))
                        @foreach ($invoice->items as $key => $item)
                            <input type="hidden" name="item_ids[]" value="{{ $item->id }}">
                            <!-- DESKTOP DESCRIPTION TABLE START -->
                            <div class="d-flex px-4 py-3 c-inv-desc item-row">
                                <div class="c-inv-desc-table w-100 d-lg-flex d-md-flex d-block">
                                    <table width="100%">
                                        <tbody>
                                            <tr class="text-dark-grey font-weight-bold f-14">
                                                <td width="50%" class="border-0 inv-desc-mbl btlr">@lang('accountings::app.menu.coa')
                                                </td>
                                                <td width="25%" class="border-0" align="right" id="type">
                                                    @lang('accountings::app.menu.debit')</td>
                                                <td width="25%" class="border-0" align="right">
                                                    @lang('accountings::app.menu.credit')
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="border-bottom-0">
                                                    <div class="select-others height-35 rounded border-0">
                                                        <select class="form-control select-picker" name="taxes[]">
                                                            @foreach ($coa as $acc_coa)
                                                                <option @if ($item->coa_id == $acc_coa->id) selected @endif
                                                                    value="{{ $acc_coa->id }}">
                                                                    {{ $acc_coa->coa }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="border-bottom-0 d-block d-lg-none d-md-none">
                                                    <textarea class="form-control f-14 border-0 w-100 mobile-description form-control " name="item_summary[]"
                                                        placeholder="@lang('placeholders.invoices.description')"></textarea>
                                                </td>
                                                <td class="border-bottom-0">
                                                    <input type="number" min="1"
                                                        class="form-control f-14 border-0 w-100 text-right item_name"
                                                        value="{{ $item->debit }}" name="item_name[]">
                                                </td>
                                                <td class="border-bottom-0">
                                                    <input type="number" min="1"
                                                        class="f-14 border-0 w-100 text-right cost_per_item form-control"
                                                        value="{{ $item->credit }}" name="cost_per_item[]">
                                                </td>
                                            </tr>
                                            <tr class="d-none d-md-table-row d-lg-table-row">
                                                <td colspan="3" class="dash-border-top bblr border">
                                                    <textarea class="f-14 border p-3 rounded w-100 desktop-description form-control" name="item_summary[]"
                                                        placeholder="@lang('placeholders.invoices.description')">{{ $item->notes }}</textarea>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <a href="javascript:;"
                                        class="d-flex align-items-center justify-content-center ml-3 remove-item"><i
                                            class="fa fa-times-circle f-20 text-lightest"></i></a>
                                </div>
                            </div>
                            <!-- DESKTOP DESCRIPTION TABLE END -->
                        @endforeach
                    @else
                        <!-- DESKTOP DESCRIPTION TABLE START -->
                        <div class="d-flex px-4 py-3 c-inv-desc item-row">
                            <div class="c-inv-desc-table w-100 d-lg-flex d-md-flex d-block">
                                <table width="100%">
                                    <tbody>
                                        <tr class="text-dark-grey font-weight-bold f-14">
                                            <td width="50%" class="border-0 inv-desc-mbl btlr">@lang('accountings::app.menu.coa')</td>
                                            <td width="25%" class="border-0" align="right" id="type">
                                                @lang('accountings::app.menu.debit')</td>
                                            <td width="25%" class="border-0" align="right">
                                                @lang('accountings::app.menu.credit')
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-bottom-0">
                                                <div class="select-others height-35 rounded border-0">
                                                    <select class="form-control select-picker" name="taxes[]">
                                                        @foreach ($coa as $acc_coa)
                                                            <option value="{{ $acc_coa->id }}">
                                                                {{ $acc_coa->coa }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td class="border-bottom-0">
                                                <input type="number" min="1"
                                                    class="form-control f-14 border-0 w-100 text-right item_name"
                                                    name="item_name[]">
                                            </td>
                                            <td class="border-bottom-0">
                                                <input type="number" min="1"
                                                    class="f-14 border-0 w-100 text-right cost_per_item form-control"
                                                    name="cost_per_item[]">
                                            </td>
                                        </tr>
                                        <tr class="d-none d-md-table-row d-lg-table-row">
                                            <td colspan="3" class="dash-border-top bblr border">
                                                <textarea class="f-14 border p-3 rounded w-100 desktop-description form-control" name="item_summary[]"
                                                    placeholder="@lang('placeholders.invoices.description')"></textarea>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <a href="javascript:;"
                                    class="d-flex align-items-center justify-content-center ml-3 remove-item"><i
                                        class="fa fa-times-circle f-20 text-lightest"></i></a>
                            </div>
                        </div>
                    @endif



                </div>
                <!--  ADD ITEM START-->
                <div class="row px-lg-4 px-md-4 px-3 pb-3 pt-0 mb-3  mt-2">
                    <div class="col-md-12">
                        <a class="f-15 f-w-500" href="javascript:;" id="add-item"><i
                                class="icons icon-plus font-weight-bold mr-1"></i>@lang('modules.invoices.addItem')</a>
                    </div>
                </div>
                <!--  ADD ITEM END-->
                <hr class="m-0 border-top-grey">
                <!-- TOTAL, DISCOUNT START -->
                <div class="d-flex px-lg-4 px-md-4 px-3 pb-3 c-inv-total">
                    <table width="100%" class="text-right f-14 text-capitalize">
                        <tbody>
                            <tr>
                                <td width="50%" class="border-0 d-lg-table d-md-table d-none"></td>
                                <td width="50%" class="p-0 border-0 c-inv-total-right">
                                    <table width="100%">
                                        <tbody>
                                            <tr>
                                                <td colspan="2" class="border-top-0 text-dark-grey">
                                                    @lang('modules.invoices.subTotal') Debit</td>
                                                <td width="30%" class="border-top-0">
                                                    <input type="text"
                                                        class="form-control-plaintext f-14 height-35 rounded w-100 text-right"
                                                        value="0" name="total_debit" id="total_debit"
                                                        class="form-control" readonly>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="border-top-0 text-dark-grey">
                                                    @lang('modules.invoices.subTotal') Kredit</td>
                                                <td width="30%" class="border-top-0">
                                                    <input type="text"
                                                        class="form-control-plaintext f-14 height-35 rounded w-100 text-right"
                                                        value="0" name="total_kredit" id="total_kredit"
                                                        class="form-control" readonly>
                                                </td>
                                            </tr>
                                            <tr class="bg-amt-grey f-16 f-w-500">
                                                <td colspan="2">@lang('modules.invoices.total')</td>
                                                <td><span class="jumlah" id="jumlah_total">0</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- TOTAL, DISCOUNT END -->

                <!-- CANCEL SAVE START -->
                <x-form-actions class="c-inv-btns d-block d-lg-flex d-md-flex">
                    <x-forms.button-primary data-type="save" class="save-form mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>

                    <x-forms.button-cancel :link="route('journal.index')" class="border-0 ">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
                <!-- CANCEL SAVE END -->

            </x-form>
            <!-- FORM END -->
        </div>
        <!-- CREATE INVOICE END -->
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            if ($('.custom-date-picker').length > 0) {
                datepicker('.custom-date-picker', {
                    position: 'bl',
                    ...datepickerConfig
                });
            }

            const dp1 = datepicker('#invoice_date', {
                position: 'bl',
                dateSelected: new Date("{{ str_replace('-', '/', $invoice->journal_date) }}"),
                ...datepickerConfig
            });

            $(document).on('click', '#add-item', function() {

                var i = $(document).find('.item_name').length;
                var item =
                    ` <div class="d-flex px-4 py-3 c-inv-desc item-row">
                <div class="c-inv-desc-table w-100 d-lg-flex d-md-flex d-block">
                <table width="100%">
                <tbody>
                <tr class="text-dark-grey font-weight-bold f-14">
                    <td width="50%" class="border-0 inv-desc-mbl btlr">
                        @lang('accountings::app.menu.coa')</td>
                    <td width="25%" class="border-0" align="right" id="type">
                        @lang('accountings::app.menu.debit')</td>
                    <td width="25%" class="border-0" align="right">
                        @lang('accountings::app.menu.credit')
                    </td>
                </tr>
                <tr>
                    <td class="border-bottom-0">
                        <div class="select-others height-35 rounded border-0">
                            <select class="form-control select-picker  height-35 f-14" name="taxes[]">
                                @foreach ($coa as $items)
                                    <option value="{{ $items->id }}">{{ $items->coa }}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td class="border-bottom-0 d-block d-lg-none d-md-none">
                        <textarea class="f-14 border-0 w-100 mobile-description form-control " name="item_summary[]" placeholder="@lang('placeholders.invoices.description')"></textarea>
                    </td>
                    <td class="border-bottom-0">
                        <input type="number" min="1" class="form-control f-14 border-0 w-100 text-right item_name" value="1" name="item_name[]">
                    </td>
                    <td class="border-bottom-0">
                        <input type="number" min="1" class="f-14 border-0 w-100 text-right" placeholder="0.00" value="0" name="cost_per_item[]">
                    </td>
                </tr>
                <tr class="d-none d-md-table-row d-lg-table-row">
                    <td colspan="3" class="dash-border-top bblr border">
                        <textarea class="f-14 border p-3 rounded w-100 desktop-description form-control" name="item_summary[]"
                            placeholder="@lang('placeholders.invoices.description')"></textarea>
                    </td>
                </tr>
                </tbody>
                </table>
                </div>
                <a href="javascript:;" class="d-flex align-items-center justify-content-center ml-3 remove-item"><i class="fa fa-times-circle f-20 text-lightest"></i></a>
                </div>`;
                $(item).hide().appendTo("#sortable").fadeIn(500);
            });

            $(document).ready(function() {
                // Hitung total saat memuat data ke dalam elemen input
                var totalDebit = 0;
                $('input[name^="item_name"]').each(function() {
                    totalDebit += parseInt($(this).val());
                });
                $('#total_debit').val(totalDebit);
                var totalKredit = 0;
                $('input[name^="cost_per_item"]').each(function() {
                    totalKredit += parseInt($(this).val());
                });
                $('#total_kredit').val(totalKredit);

                // Jalankan hitungSelisih() setelah kedua total dihitung
                hitungSelisih();
            });

            $(document).on('keyup', 'input[name^="item_name"]', function() {
                var total = 0;
                $('input[name^="item_name"]').each(function() {
                    total += parseInt($(this).val());
                });
                $('#total_debit').val(total);
                hitungSelisih();
            });

            $(document).on('keyup', 'input[name^="cost_per_item"]', function() {
                var total = 0;
                $('input[name^="cost_per_item"]').each(function() {
                    total += parseInt($(this).val());
                });
                $('#total_kredit').val(total);
                hitungSelisih();
            });

            function hitungSelisih() {
                // Ambil nilai total debit dan total kredit dari elemen input
                const totalDebit = Number($('#total_debit').val());
                const totalKredit = Number($('#total_kredit').val());

                // Hitung selisih antara total debit dan total kredit
                const selisih = totalDebit - totalKredit;

                // Tampilkan hasil selisih di dalam elemen span
                const totalSpan = document.querySelector('span.jumlah');
                totalSpan.textContent = selisih;
            }

            $('#saveInvoiceForm').on('click', '.remove-item', function() {
                $(this).closest('.item-row').fadeOut(300, function() {
                    $(this).remove();
                });
            });

            $('.save-form').click(function() {
                $.easyAjax({
                    url: "{{ route('journal.update', $invoice->id) }}" + "?type=" + type,
                    container: '#saveInvoiceForm',
                    type: "POST",
                    blockUI: true,
                    redirect: true,
                    file: true, // Commented so that we dot get error of Input variables exceeded 1000
                    data: $('#saveInvoiceForm').serialize(),
                    success: function(response) {
                        if (response.status === 'success') {
                            window.location.href = response.redirectUrl;
                        }
                    }
                })
            });

            $('#saveScheduleForm').on('click', '.remove-item', function() {
                $(this).closest('.item-row').fadeOut(300, function() {
                    $(this).remove();
                    $('select.customSequence').each(function(index) {
                        $(this).attr('name', 'taxes[' + index + '][]');
                        $(this).attr('id', 'multiselect' + index + '');
                    });
                    calculateTotal();
                });
            });

            $('#add-type').click(function() {
                const url = "{{ route('journal-type.create') }}";
                $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                $.ajaxModal(MODAL_LG, url);
            });

            calculateTotal();

            init(RIGHT_MODAL);
        });
    </script>
@endpush
