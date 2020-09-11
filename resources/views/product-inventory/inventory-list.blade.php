@extends('layouts.app')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style>

    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Inventory Data</h2>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-responsive" id="inventory-data">
        <table class="table table-bordered infinite-scroll">
            <thead>
            <tr>
                <th>Id</th>
                <th>Sku</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Supplier</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @include("product-inventory.inventory-list-partials.grid")
            </tbody>
        </table>
    </div>
    <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />


    <div id="medias-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Medias</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <div id="status-history-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Status History</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script>



        $('.show-medias-modal').click(function() {
            let data = $(this).parent().parent().find('.medias-data').attr('data')

            let result = '';

            if(data != '[]') {
                data = JSON.parse(data)

                result += '<table class="table table-bordered">';
                result += '<thead><th>Directory</th><th>filename</th><th>extension</th><th>disk</th></thead>';
                result += '<tbody>';
                for(let value in data) {
                    result += '<tr>';
                    result += "<td>"+data[value].directory+"</td>"
                    result += "<td>"+data[value].filename+"</td>"
                    result += "<td>"+data[value].extension+"</td>"
                    result += "<td>"+data[value].disk+"</td>"
                    result += '</tr>';
                }
                result += '</tbody>';
                result += '</table>';

            } else {
                result = '<h3>this product dont have any media</h3>';
            }

            $('#medias-modal .modal-body').html(result)

            $('#medias-modal').modal('show')
        })

        $('.show-status-history-modal').click(function() {

            let data = $(this).parent().parent().find('.status-history').attr('data')
            let result = '';

            if(data != '[]') {
                data = JSON.parse(data)

                result += '<table class="table table-bordered">';
                result += '<thead><th>old status</th><th>new status</th><th>created at</th></thead>';
                result += '<tbody>';
                for(let value in data) {
                    result += '<tr>';
                    result += "<td>"+data[value].old_status+"</td>"
                    result += "<td>"+data[value].new_status+"</td>"
                    result += "<td>"+data[value].created_at+"</td>"
                    result += '</tr>';
                }
                result += '</tbody>';
                result += '</table>';

            } else {
                result = '<h3>this Product dont have status history</h3>';
            }

            $('#status-history-modal .modal-body').html(result)

            $('#status-history-modal').modal('show')
        })


        var isLoadingProducts = false;
        let page = 1;

        $(function () {
            $(window).scroll(function() {
                if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {

                    loadMoreProducts();

                }
            });
        });

        function loadMoreProducts() {
            if (isLoadingProducts) return;

            isLoadingProducts = true;

            var loader = $('.infinite-scroll-products-loader');
            page++;
            console.log('{{ url('productinventory/inventory-list?page=') }}'+page)
            $.ajax({
                url: '{{ url('productinventory/inventory-list?page=') }}'+page,
                type: 'GET',
                beforeSend: function() {
                    loader.show();
                }
            })
                .done(function(data) {
                    loader.hide();
                    if('' === data.trim()) return;
                    $('#inventory-data tbody').append(data);
                    isLoadingProducts = false;
                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    console.error('something went wrong');
                    isLoadingProducts = false;
                });
        }
    </script>
@endsection