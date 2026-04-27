
<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('modules.estimates.signatureAndConfirmation')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">

    <x-form id="accept">
        <div class="row">
            <input type="hidden" value="accept" name="status" id="status">
            <div class="col-sm-12 py-20">
                <x-recruit::cards.custom-question-field :fields="$fields" :values="$values" />
            </div>
            <div class="col-sm-12 bg-grey p-4 signature">
                <x-forms.label fieldId="signature-pad" fieldRequired=""
                               :fieldLabel="__('modules.estimates.signature')"/>
                <div class="signature_wrap wrapper border-0 form-control">
                    <canvas id="signature-pad" class="signature-pad rounded" width=400 height=150></canvas>
                </div>
            </div>
            <div class="col-sm-12 mt-3">
                <x-forms.button-secondary id="undo-signature">@lang('modules.estimates.undo')</x-forms.button-secondary>
                <x-forms.button-secondary class="ml-2"
                                          id="clear-signature">@lang('modules.estimates.clear')</x-forms.button-secondary>
            </div>

        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-signature"
                            icon="check">@lang('recruit::modules.joboffer.submit')</x-forms.button-primary>
</div>

<script>
    init(MODAL_LG);
    function checkboxChange(parentClass, id) {
        var checkedData = '';
        $('.' + parentClass).find("input[type= 'checkbox']:checked").each(function() {
            checkedData = (checkedData !== '') ? checkedData + ', ' + $(this).val() : $(this).val();
        });
        $('#' + id).val(checkedData);
    }

    datepicker('.custom-date-picker', {
        position: 'bl',
        formatter: (input, date, instance) => {
        input.value = moment(date).format('{{ $company->moment_date_format }}')
    },
    showAllDates: true,
        customDays: {!!  json_encode(\App\Models\GlobalSetting::getDaysOfWeek())!!},
        customMonths: {!!  json_encode(\App\Models\GlobalSetting::getMonthsOfYear())!!},
        customOverlayMonths: {!!  json_encode(\App\Models\GlobalSetting::getMonthsOfYear())!!},
        overlayButton: "@lang('app.submit')",
        overlayPlaceholder: "@lang('app.enterYear')",
    });

</script>

<script src="{{ asset('vendor/jquery/signature_pad.min.js') }}"></script>
<script>
    const datepickerConfig = {
        formatter: (input, date, instance) => {
            input.value = moment(date).format('{{ $company->moment_date_format }}')
        },
        showAllDates: true,
        customDays: {!!  json_encode(\App\Models\GlobalSetting::getDaysOfWeek())!!},
        customMonths: {!!  json_encode(\App\Models\GlobalSetting::getMonthsOfYear())!!},
        customOverlayMonths: {!!  json_encode(\App\Models\GlobalSetting::getMonthsOfYear())!!},
        overlayButton: "@lang('app.submit')",
        overlayPlaceholder: "@lang('app.enterYear')",
        startDay: parseInt("{{ attendance_setting()->week_start_from }}")
    };

    const daterangeConfig = {
        "@lang('app.today')": [moment(), moment()],
        "@lang('app.last30Days')": [moment().subtract(29, 'days'), moment()],
        "@lang('app.thisMonth')": [moment().startOf('month'), moment().endOf('month')],
        "@lang('app.lastMonth')": [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
            .endOf(
                'month')
        ],
        "@lang('app.last90Days')": [moment().subtract(89, 'days'), moment()],
        "@lang('app.last6Months')": [moment().subtract(6, 'months'), moment()],
        "@lang('app.last1Year')": [moment().subtract(1, 'years'), moment()]
    };

    var canvas = document.getElementById('signature-pad');

    var signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)' // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
    });

    document.getElementById('clear-signature').addEventListener('click', function (e) {
        e.preventDefault();
        signaturePad.clear();
    });

    document.getElementById('undo-signature').addEventListener('click', function (e) {
        e.preventDefault();
        var data = signaturePad.toData();
        if (data) {
            data.pop(); // remove the last dot or line
            signaturePad.fromData(data);
        }
    });

    $('body').on('click', '.img-lightbox', function () {
        var imageUrl = $(this).data('image-url');
        const url = "{{ route('front.public.show_image') . '?image_url=' }}" + imageUrl;
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    $('body').on('click', '#save-signature', function () {
        var status = 'accept';
        var signature = signaturePad.toDataURL('image/png');
        var signatureReq = '{{ $jobOffer->sign_require }}';
        if (signaturePad.isEmpty() && signatureReq == "on") {

            Swal.fire({
                icon: 'error',
                text: '{{ __('messages.signatureRequired') }}',

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

        $.easyAjax({
            url: "{{ route('front.job-offer.accept', $jobOffer->id) }}",
            container: '#accept',
            type: "POST",
            disableButton: true,
            blockUI: true,
            file: true,
            redirect: true,
            data:{
                signature: signature,
            },
            success: function (response) {
                if (response.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });

</script>


