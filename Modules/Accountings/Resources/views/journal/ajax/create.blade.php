@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
<!-- CREATE INVOICE START -->
<div class="bg-white rounded b-shadow-4 create-inv">
    <!-- HEADING START -->
    <div class="px-lg-4 px-md-4 px-3 py-3">
        <h4 class="mb-0 f-21 font-weight-normal text-capitalize">@lang('accountings::app.acc.addJournal') @lang('app.details')</h4>
    </div>
    <!-- HEADING END -->
    <hr class="m-0 border-top-grey">
    <!-- FORM START -->
    <x-form class="c-inv-form" id="saveInvoiceForm">
        <div class="row px-lg-4 px-md-4 px-3 py-3">
            <div class="col-md-2">
                <div class="form-group mb-lg-0 mb-md-0 mb-4">
                    <x-forms.label class="mb-12" fieldId="invoice_number" :fieldLabel="__('accountings::app.menu.journalNo')" fieldRequired="true">
                    </x-forms.label>
                    <x-forms.input-group>
                        <input type="text" name="invoice_number" id="invoice_number"
                            class="form-control height-35 f-15"
                            value="{{ date('ym') }}-{{ is_null($lastInvoice) ? 1 : $lastInvoice }}">
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
                            value="{{ now(company()->timezone)->translatedFormat(company()->date_format) }}">
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
                                <option value="{{ $items->id }}">{{ $items->type_journal }}</option>
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
                    :fieldPlaceholder="__('')">
                </x-forms.text>
            </div>

            <div class="col-md-6">
                <x-forms.text fieldId="remark" :fieldLabel="__('accountings::app.menu.remark')" fieldName="remark" fieldRequired="true"
                    :fieldPlaceholder="__('')">
                </x-forms.text>
            </div>
        </div>
        <!-- CLIENT, PROJECT, GST, BILLING, SHIPPING ADDRESS END -->
        <hr class="m-0 border-top-grey">

        <div id="sortable">
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
                                                <option value="{{ $acc_coa->id }}">{{ $acc_coa->coa }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td class="border-bottom-0">
                                    <input type="number" min="1"
                                        class="form-control f-14 height-35 rounded border-0 w-100 text-right item_name"
                                        value="0" name="item_name[]">
                                </td>
                                <td class="border-bottom-0">
                                    <input type="number" min="1"
                                        class="form-control f-14 height-35 rounded w-100 text-right" value="0"
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

                    <a href="javascript:;" class="d-flex align-items-center justify-content-center ml-3 remove-item"><i
                            class="fa fa-times-circle f-20 text-lightest"></i></a>
                </div>
            </div>
            <!-- DESKTOP DESCRIPTION TABLE END -->

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
                    <td class="border-bottom-0">
                        <input type="number" min="1" class="form-control height-35 rounded text-right" value="0" name="item_name[]">
                    </td>
                    <td class="border-bottom-0">
                        <input type="number" min="1" class="form-control height-35 rounded text-right" value="0" name="cost_per_item[]">
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
            $('#multiselect' + i).selectpicker();
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
                calculateTotal();
            });
        });

        $('.save-form').click(function() {
            var type = $(this).data('type');
            var jumlah = $('#jumlah_total').html();

            if ((jumlah) == 0) {

                if (KTUtil.isMobileDevice()) {
                    $('.desktop-description').remove();
                } else {
                    $('.mobile-description').remove();
                }

                $.easyAjax({
                    url: "{{ route('journal.store') }}" + "?type=" + type,
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
            } else {
                Swal.fire({
                    icon: 'error',
                    text: "Data tidak valid",

                    customClass: {
                        confirmButton: 'btn btn-primary',
                    },
                    showClass: {
                        popup: 'swal2-noanimation',
                        backdrop: 'swal2-noanimation'
                    },
                    buttonsStyling: false
                });
                return false;
            }
        });

        $('#saveInvoiceForm').on('click', '.remove-item', function() {
            $(this).closest('.item-row').fadeOut(300, function() {
                $(this).remove();
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
