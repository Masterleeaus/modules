<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('recruit::modules.joboffer.decline')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">

    <x-form id="decline">
        <div class="row">
            <div class="col-md-12">
                <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                  :fieldLabel="__('recruit::modules.joboffer.reason')" fieldName="reason" :
                                  fieldId="reason">
                </x-forms.textarea>
            </div>


        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="decline-offer-letter"
                            icon="check">@lang('recruit::modules.joboffer.submit')</x-forms.button-primary>
</div>

