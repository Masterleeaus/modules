<!-- Offcanvas to add/update designation -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddOrUpdateDesignation"
    aria-labelledby="offcanvasDesignationLabel">
    <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasDesignationLabel" class="offcanvas-title">{{ __('Add Designation') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
        <form class="pt-0" id="designationForm">
            <input type="hidden" name="id" id="id">
            
            <div class="mb-6">
                <label class="form-label" for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" placeholder="{{ __('Enter designation name') }}" 
                    name="name" required />
            </div>

            <div class="mb-6">
                <label class="form-label" for="code">{{ __('Code') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="code" placeholder="{{ __('Enter designation code') }}" 
                    name="code" required />
            </div>

            <div class="mb-6">
                <label class="form-label" for="department_id">{{ __('Department') }}</label>
                <select class="form-select select2" id="department_id" name="department_id">
                    <option value="">{{ __('Select Department') }}</option>
                    @if(isset($departments))
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mb-6">
                <label class="form-label" for="notes">{{ __('Notes') }}</label>
                <textarea class="form-control" id="notes" name="notes" 
                    placeholder="{{ __('Enter notes') }}" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary me-3 data-submit">{{ __('Create') }}</button>
            <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
        </form>
    </div>
</div>