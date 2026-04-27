<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddOrUpdateHoliday"
    aria-labelledby="offcanvasCreateHolidayLabel">
    <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasHolidayLabel" class="offcanvas-title">@lang('Create Holiday')</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
        <form class="pt-0" id="holidayForm">
            <input type="hidden" name="id" id="id">
            <div class="mb-6">
                <label class="form-label" for="name">@lang('Name')<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" placeholder="@lang('Enter name')"
                    name="name" />
            </div>
            <div class="mb-6">
                <label class="form-label" for="code">@lang('Code')<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="code" placeholder="@lang('Enter code')"
                    name="code" />
            </div>
            <div class="mb-6">
                <label class="form-label" for="date">@lang('Date')<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="date" placeholder="@lang('Select date')"
                    name="date" />
            </div>
            <div class="mb-6">
                <label class="form-label" for="notes">@lang('Notes')</label>
                <textarea class="form-control" id="notes" placeholder="@lang('Enter notes')" name="notes" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary me-3 data-submit">@lang('Submit')</button>
            <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">@lang('Cancel')</button>
        </form>
    </div>
</div>