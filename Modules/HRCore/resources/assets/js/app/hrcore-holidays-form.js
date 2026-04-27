$(function () {
    // Initialize date pickers
    $('.flatpickr-date').flatpickr({
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'F j, Y'
    });

    $('.flatpickr-time').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        time_24hr: true
    });

    // Initialize Select2 for departments
    $('#departments').select2({
        placeholder: 'Select departments',
        allowClear: true
    });

    // Toggle functions
    window.toggleHalfDayFields = function () {
        if ($('#is_half_day').is(':checked')) {
            $('#halfDayFields').slideDown();
        } else {
            $('#halfDayFields').slideUp();
        }
    };

    window.toggleCompensatoryFields = function () {
        if ($('#is_compensatory').is(':checked')) {
            $('#compensatoryFields').slideDown();
        } else {
            $('#compensatoryFields').slideUp();
        }
    };

    window.toggleNotificationFields = function () {
        if ($('#send_notification').is(':checked')) {
            $('#notificationFields').slideDown();
        } else {
            $('#notificationFields').slideUp();
        }
    };

    window.toggleApplicabilityFields = function () {
        const value = $('#applicable_for').val();
        
        // Hide all fields first
        $('#departmentFields, #locationFields').hide();
        
        // Show relevant fields
        if (value === 'department') {
            $('#departmentFields').slideDown();
        } else if (value === 'location') {
            $('#locationFields').slideDown();
        }
    };

    // The controller handles checkbox values properly using $request->has()
    // No need for special checkbox handling in JavaScript
});