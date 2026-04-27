$(function () {
    // CSRF setup
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Initialize DataTable
    let dataTable = $('#holidays-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: pageData.urls.datatable,
            data: function (d) {
                d.year = $('#filter-year').val();
                d.type = $('#filter-type').val();
                d.status = $('#filter-status').val();
            }
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'date_formatted', name: 'date' },
            { data: 'type_badge', name: 'type' },
            { data: 'applicability', name: 'applicable_for' },
            { data: 'tags', name: 'tags', orderable: false },
            { data: 'status_badge', name: 'is_active' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']],
        language: {
            paginate: {
                previous: '<i class="bx bx-chevron-left bx-sm"></i>',
                next: '<i class="bx bx-chevron-right bx-sm"></i>'
            }
        }
    });


    // Global functions
    window.editHoliday = function (id) {
        window.location.href = pageData.urls.edit.replace(':id', id);
    };

    window.deleteHoliday = function (id) {
        Swal.fire({
            title: 'Are you sure?',
            text: pageData.labels.confirmDelete,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: pageData.urls.destroy.replace(':id', id),
                    type: 'DELETE',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire('Deleted!', response.data.message || 'Holiday has been deleted.', 'success');
                            dataTable.ajax.reload(null, false);
                        } else {
                            Swal.fire('Error!', response.data || pageData.labels.error, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error!', 'Failed to delete holiday', 'error');
                    }
                });
            }
        });
    };

    window.toggleStatus = function (id) {
        Swal.fire({
            title: pageData.labels.confirmStatusChange,
            text: 'Do you want to change the status of this holiday?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, change it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: pageData.urls.toggleStatus.replace(':id', id),
                    type: 'POST',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire('Success!', response.data.message || response.data || pageData.labels.success, 'success');
                            dataTable.ajax.reload(null, false);
                        } else {
                            Swal.fire('Error!', response.data || pageData.labels.error, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error!', pageData.labels.error, 'error');
                    }
                });
            }
        });
    };


    window.applyFilters = function () {
        dataTable.ajax.reload();
    };

    window.resetFilters = function () {
        $('#filter-year').val($('#filter-year option:selected').val());
        $('#filter-type').val('');
        $('#filter-status').val('');
        dataTable.ajax.reload();
    };
});