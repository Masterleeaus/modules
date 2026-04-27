<!-- ROW START -->
<div class="row">
    <!--  USER CARDS START -->
    <div class="col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4">
        <div class="d-flex" id="table-actions">
            @if ($addPermission == 'all' || $addPermission == 'added')
                <x-forms.button-primary icon="plus" id="add-product-transaction" class="mr-3">
                    @lang('purchase::modules.product.newTransaction')
                </x-forms.button-primary>
            @endif
        </div>
        @if ($viewPermission == 'all' || $viewPermission == 'added')
            <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
                {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}
            </div>
        @endif
    </div>
    <!--  USER CARDS END -->
</div>
<!-- ROW END -->

@include('sections.datatable_js')

<script>
    $('#transactions-table').on('preXhr.dt', function(e, settings, data) {
        var productId = "{{ $product->id }}";
        data['productId'] = productId;
    });

    const showTable = () => {
        window.LaravelDataTables["transactions-table"].draw(true);
    }

    $('body').on('click', '.delete-table-row-transaction', function() {
        return false;
    });

    $('#add-product-transaction').click(function() {
        return false;
    })
</script>
