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
                    <x-forms.text fieldId="title" :fieldLabel="__('recruit::modules.footerlinks.linktitle')"
                                  fieldName="title" fieldRequired="true"
                                  :fieldPlaceholder="__('recruit::modules.footerlinks.linktitle')">
                    </x-forms.text>
                </div>
                <div class="col-md-4">
                    <x-forms.text fieldId="slug" :fieldLabel="__('recruit::modules.footerlinks.slug')"
                                  fieldName="slug" fieldRequired="true"
                                  :fieldPlaceholder="__('recruit::modules.footerlinks.slug')">
                    </x-forms.text>
                </div>

                <div class="col-md-4">
                    <x-forms.select fieldId="status" :fieldLabel="__('app.status')" fieldName="status"
                                    search="true">
                        <option value="active"
                                data-content="<i class='fa fa-circle mr-2 green-dot'></i> @lang('app.active')"></option>
                        <option value="inactive"
                                data-content="<i class='fa fa-circle mr-2 red-dot'></i> @lang('app.inactive')"></option>
                    </x-forms.select>
                </div>


                <div class="col-md-12">
                    <div class="form-group my-3">
                        <x-forms.label class="my-3" fieldId="description"
                                       :fieldLabel="__('recruit::modules.footerlinks.description')">
                        </x-forms.label>
                        <div id="job_description"></div>
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

    // save recruiter
    $('body').off('click', "#save-footer").on('click', '#save-footer', function () {
        var jobDescription = document.getElementById('job_description').children[0].innerHTML;
        document.getElementById('description-text').value = jobDescription;

        $.easyAjax({
            url: "{{ route('footer-settings.store') }}",
            container: '#createMethods',
            type: "POST",
            blockUI: true,
            data: $('#createMethods').serialize(),
            success: function (response) {
                if (response.status == "success") {
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

        quillImageLoad('#job_description');

    });
</script>
