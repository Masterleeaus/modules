<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddOrUpdateShift"
    aria-labelledby="offcanvasCreateShiftLabel">
    <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasShiftLabel" class="offcanvas-title">@lang('Create Shift')</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
        <form class="pt-0" id="shiftForm">
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
            <div class="row mb-6">
                <div class="col-md-6">
                    <label for="start_time" class="form-label">@lang('Start Time')<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="start_time" name="start_time" placeholder="HH:MM" />
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label">@lang('End Time')<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="end_time" name="end_time" placeholder="HH:MM" />
                </div>
            </div>
            <div class="mb-6">
                <label class="form-label d-block">@lang('Working Days')</label>
                <div class="d-flex flex-wrap gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="monday" name="monday" checked>
                        <label class="form-check-label" for="monday">@lang('Monday')</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="tuesday" name="tuesday" checked>
                        <label class="form-check-label" for="tuesday">@lang('Tuesday')</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="wednesday" name="wednesday" checked>
                        <label class="form-check-label" for="wednesday">@lang('Wednesday')</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="thursday" name="thursday" checked>
                        <label class="form-check-label" for="thursday">@lang('Thursday')</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="friday" name="friday" checked>
                        <label class="form-check-label" for="friday">@lang('Friday')</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="saturday" name="saturday">
                        <label class="form-check-label" for="saturday">@lang('Saturday')</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="sunday" name="sunday">
                        <label class="form-check-label" for="sunday">@lang('Sunday')</label>
                    </div>
                </div>
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