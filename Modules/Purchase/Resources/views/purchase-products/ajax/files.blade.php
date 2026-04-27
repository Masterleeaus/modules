<!-- ROW START -->
<div class="row">
    <!--  USER CARDS START -->
    <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0">
        <div class="d-flex">
            <div id="table-actions" class="flex-grow-1 align-items-center">
                @if ($addPermission == 'all' || $addPermission == 'added')
                    <x-forms.link-primary link="javascript:;" id="add-files" class="mr-3 float-left" icon="plus"
                        data-product-id="{{ $product->id }}">
                        @lang('purchase::modules.product.addImages')
                    </x-forms.link-primary>
                @endif
            </div>

            <div class="btn-group" role="group">
                <a id="list-tabs" href="javascript:;" onclick="productFilesView('listview')"
                    class="btn btn-secondary f-14 layout btn-active" data-toggle="tooltip" data-tab-name="listview"
                    data-original-title="List View"><i class="side-icon bi bi-list-ul"></i></a>

                <a id="thumbnail" href="javascript:;" onclick="productFilesView('gridview')"
                    class="btn btn-secondary f-14 layout" data-toggle="tooltip" data-tab-name="gridview"
                    data-original-title="Grid View"><i class="side-icon bi bi-grid"></i></a>
            </div>
        </div>

        <div class="d-flex flex-column mt-3">
            <div id="layout">
                @include('purchase::purchase-products.product-files.ajax-list')
            </div>

        </div>
    </div>
    <!--  USER CARDS END -->
</div>
<!-- ROW END -->


<script>
    var fileLayout = 'listview';
    
    function productFilesView(layout) {
        $('#layout').html('');
        var productID = "{{ $product->id }}";
        fileLayout = layout;
        $.easyAjax({
            type: 'GET',
            url: "{{ route('purchase_products.layout') }}",
            disableButton: true,
            blockUI: true,
            data: {
                id: productID,
                layout: layout
            },
            success: function(response) {
                $('#layout').html(response.html);
                if (layout == 'gridview') {
                    $('#list-tabs').removeClass('btn-active');
                    $('#thumbnail').addClass('btn-active');
                } else {
                    $('#list-tabs').addClass('btn-active');
                    $('#thumbnail').removeClass('btn-active');
                }
            }
        });
    }

    $('body').on('click', '.delete-product-file', function() {
        var id = $(this).data('file-id');
        var deleteView = $(this).data('pk');
        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.removeFileText')",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('messages.confirmDelete')",
            cancelButtonText: "@lang('app.cancel')",
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('product-files.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    blockUI: true,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            productFilesView(fileLayout);
                        }
                    }
                });
            }
        });
    });
</script>
