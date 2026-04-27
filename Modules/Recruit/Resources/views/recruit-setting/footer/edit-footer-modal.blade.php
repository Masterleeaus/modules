<style>
    .green-dot {
        color: green;
    }

    .red-dot {
        color: red;
    }
</style>
<div class="modal-header">
    <h5 class="modal-title">@lang('recruit::modules.footerlinks.addfooterlinks')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="createMethods" method="POST" class="ajax-form">
            <div class="row">
                <div class="col-md-4">
                    <input type="hidden" name="id" value='{{ $footerLink->id }}'/>
                    <x-forms.text fieldId="title" :fieldLabel="__('recruit::modules.job.jobTitle')"
                                  fieldName="title" :fieldValue="$footerLink->title" fieldRequired="true"
                                  :fieldPlaceholder="__('recruit::modules.job.jobTitle')">
                    </x-forms.text>
                </div>
                <div class="col-md-4">
                    <x-forms.text fieldId="slug" :fieldValue="$footerLink->slug"
                                  :fieldLabel="__('recruit::modules.footerlinks.slug')"
                                  fieldName="slug" fieldRequired="true"
                                  :fieldPlaceholder="__('recruit::modules.footerlinks.slug')">
                    </x-forms.text>
                </div>

                <div class="col-md-4">
                    <x-forms.select fieldId="status" :fieldLabel="__('app.status')" fieldName="status"
                                    search="true">
                        <option value=""> --</option>
                        <option @if($footerLink->status == 'active') selected @endif value="active"
                                data-content="<i class='fa fa-circle mr-2 green-dot'></i> @lang('app.active')"></option>
                        <option @if($footerLink->status == 'inactive') selected @endif value="inactive"
                                data-content="<i class='fa fa-circle mr-2 red-dot'></i> @lang('app.inactive')"></option>
                    </x-forms.select>
                </div>


                <div class="col-md-12">
                    <div class="form-group my-3">
                        <x-forms.label class="my-3" fieldId="description-textt"
                                       :fieldValue="$footerLink->job_description"
                                       :fieldLabel="__('recruit::modules.job.jobDescription')">
                        </x-forms.label>
                        <div id="description">{!! $footerLink->description !!}</div>
                        <textarea name="description" id="description-text" class="d-none"></textarea>
                    </div>
                </div>
            </div>
        </x-form>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-footer" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $(".select-picker").selectpicker();

    $('body').off('click', "#save-footer").on('click', '#save-footer', function () {

        var jobDescription = document.getElementById('description').children[0].innerHTML;
        document.getElementById('description-text').value = jobDescription;

        $.easyAjax({
            container: '#createMethods',
            type: "PUT",
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-footer",
            url: "{{ route('footer-settings.update', $footerLink->id) }}",
            data: $('#createMethods').serialize(),
            success: function (response) {
                if (response.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });

    $(document).ready(function () {

        function createSlug(value) {
            value = value.replace(/\s\s+/g, ' ');
            let slug = value.split(' ').join('-').toLowerCase();
            slug = slug.replace(/--+/g, '-');
            $('#slug').val(slug);
        }

        $('#title').keyup(function (e) {
            createSlug($(this).val());
        });

        $('#slug').keyup(function (e) {
            createSlug($(this).val());
        });

        quillImageLoad('#description');

    });
</script>
