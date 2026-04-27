<!-- Offcanvas to add new department -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddDepartment"
     aria-labelledby="offcanvasAddDepartmentLabel">
  <div class="offcanvas-header border-bottom">
    <h5 id="offcanvasAddDepartmentLabel" class="offcanvas-title">{{ __('Add Department') }}</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
    <form class="pt-0" id="addNewDepartmentForm">
      <input type="hidden" name="departmentId" id="departmentId">

      <div class="mb-6">
        <label class="form-label" for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="name" placeholder="{{ __('Enter Department Name') }}" name="name"
               required />
      </div>

      <div class="mb-6">
        <label class="form-label" for="parent_department">{{ __('Parent Department') }}</label>
        <select class="form-select select2" id="parent_department" name="parent_department">
          <option value="">{{ __('Select parent department') }}</option>
        </select>
      </div>

      <div class="mb-6">
        <label class="form-label" for="code">{{ __('Code') }} <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="code" placeholder="{{ __('Enter Department Code') }}" name="code"
               required />
      </div>

      <div class="mb-6">
        <label class="form-label" for="notes">{{ __('Description') }}</label>
        <textarea class="form-control" id="notes" name="notes" placeholder="{{ __('Enter Description') }}" rows="3"></textarea>
      </div>

      <button type="submit" class="btn btn-primary me-3 data-submit">{{ __('Submit') }}</button>
      <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
    </form>
  </div>
</div>
