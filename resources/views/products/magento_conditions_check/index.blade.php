@extends('layouts.app')
@section('title', 'Conditions checked Products')

@section('large_content')
    @include('partials.flash_messages')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2 class="page-heading">Conditions checked Products ({{ $productsCount }})</h2>
            <div class="row">
                <div class="col-md-12 mt-3">
                    <button class="btn btn-secondary float-right push-to-magento">Push to magento</button>
                </div>
            </div>
            <div class="infinite-scroll table-responsive mt-3 infinite-scroll-data">
                @include("products.magento_conditions_check.list")
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
        $(document).on('click', '.push-to-magento', function () {
            $(self).hide();
            $this = $(this);
            var ajaxes = [];
            var thiss = $(this);
            $(this).addClass('fa-spinner').removeClass('fa-upload')
            url = "{{ route('products.pushToMagento') }}";
            ajaxes.push($.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function () {
                    $(thiss).text('Loading...');
                    $(thiss).html('<i class="fa fa-spinner" aria-hidden="true"></i>');
                }
            }).done(function (response) {
                if(response.code == 500)
                    toastr['error'](response.message, 'Error')
                else
                    toastr['success'](response.message, 'Success')
                $('#product' + id).hide();
            }).fail(function (response) {
                console.log(response);
                thiss.removeClass('fa-spinner').addClass('fa-upload')
                toastr['error']('Internal server error', 'Failure')
                $('#product' + id).hide();
                //alert('Could not update product on magento');
            }));
            $.when.apply($, ajaxes)
                .done(function () {
                    //location.reload();
                });
        });

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
