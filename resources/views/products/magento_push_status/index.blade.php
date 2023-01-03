@extends('layouts.app')
@section('title', 'Magento push status')

@section('large_content')
    @include('partials.flash_messages')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2 class="page-heading">Magento push status ({{ $productsCount }})</h2>
            <div class="infinite-scroll table-responsive mt-5 infinite-scroll-data">
                @include("products.magento_push_status.list")
            </div>
            <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />
        </div>
    </div>
    <div id="conditionCheckLogModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h3 class="modal-title">Product conditions check logs</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Message</th>
                            <th scope="col">Status</th>
                            <th scope="col">Date</th>
                        </tr>
                        </thead>
                        <tbody id="logData">


                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function getConditionCheckLog(pID, swId) {
            $.ajax({
                url: "{{ url('products/listing/conditions-check-logs/') }}" + "/" + pID + "/" + swId,
                type: "get"
            }).done(function(response) {
                if (response.code = '200') {
                    let html = '';
                    $.each(response.data, function (key, val) {
                        html += '<tr><td>' + val.id + '</td>' +
                            '<td>' + val.message + '</td>' +
                            '<td>' + val.response_status + '</td>' +
                            '<td>' + val.created_at + '</td>' +
                            '</tr>';
                    });
                    $('#logData').html(html);
                    $('#conditionCheckLogModal').modal('show');
                } else {
                    toastr['error'](response.message, 'error');
                }
            }).fail(function(errObj) {
                $('#loading-image').hide();
                $("#todolistUpdateModal").hide();
                toastr['error'](errObj.message, 'error');
            });
        }
    </script>
@endsection

