<div class="row">
    <div class="col-sm-12">
        <x-form id="save-device-form">
            @include('sections.password-autocomplete-hide')

            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal border-bottom-grey">
                    @lang('biometric::app.addBiometricDevice')</h4>

                <div class="row p-20">
                    <!-- Information Section -->
                    <div class="col-lg-12 mb-3">
                        <div class="bg-light p-3 rounded">
                            <h5 class="text-dark mb-3"><i class="fa fa-info-circle mr-2"></i>@lang('biometric::app.howToFindDeviceSerialNumber')</h5>
                            <div class="alert alert-info">
                                <strong>@lang('biometric::app.note'):</strong> @lang('biometric::app.serialNumberLocationInfo')
                            </div>
                            <div class="text-center mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <a href="javascript:;" class="open-image-modal" data-image-url="https://public.froid.works/zkteco-menu-full.jpeg">
                                            <img src="https://public.froid.works/zkteco-menu.png" alt="@lang('biometric::app.step1Menu')" class="img-fluid rounded" style="max-height: 150px;">
                                            <p class="small text-muted mt-1">@lang('biometric::app.step1AccessMenu')</p>
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="javascript:;" class="open-image-modal" data-image-url="https://public.froid.works/zkteco-system-info-full.jpeg">
                                            <img src="https://public.froid.works/zkteco-system-info.png" alt="@lang('biometric::app.step2SystemInfo')" class="img-fluid rounded" style="max-height: 150px;">
                                            <p class="small text-muted mt-1">@lang('biometric::app.step2SelectSystemInfo')</p>
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="javascript:;" class="open-image-modal" data-image-url="https://public.froid.works/zkteco-sr-full.png">
                                            <img src="https://public.froid.works/zkteco-sr.png" alt="@lang('biometric::app.step3DeviceInfo')" class="img-fluid rounded" style="max-height: 150px;">
                                            <p class="small text-muted mt-1">@lang('biometric::app.step3FindSerialNumber')</p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Form Section -->
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <x-forms.text fieldId="device_name" :fieldLabel="__('biometric::app.deviceName')" fieldName="device_name"
                                            fieldRequired="true" :fieldPlaceholder="__('biometric::app.deviceNamePlaceholder')">
                                </x-forms.text>
                                <small class="form-text text-muted">@lang('biometric::app.deviceNameDescription')</small>
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <x-forms.text fieldId="serial_number" :fieldLabel="__('biometric::app.serialNumber')"
                                            fieldName="serial_number" fieldRequired="true"
                                            :fieldPlaceholder="__('biometric::app.serialNumberPlaceholder')">
                                </x-forms.text>
                                <small class="form-text text-muted">@lang('biometric::app.serialNumberDescription')</small>
                            </div>
                        </div>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-device" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('biometric-devices.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('biometric::app.deviceSerialNumberLocation')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalImage" class="img-fluid" alt="@lang('biometric::app.serialNumberLocation')">
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Handle image modal
        $('.open-image-modal').click(function() {
            var imageUrl = $(this).data('image-url');
            $('#modalImage').attr('src', imageUrl);
            $('#imageModal').modal('show');
        });

        $('#save-device').click(function() {
            const url = "{{ route('biometric-devices.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-device-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-device",
                data: $('#save-device-form').serialize(),
                success: function(response) {
                    if (response.status == "success") {
                        window.location.href = response.redirectUrl;
                    }
                }
            });
        });

        init(RIGHT_MODAL);
    });
</script>
