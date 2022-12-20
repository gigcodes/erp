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

        function getConditionCheckLog(llmId) {
            $.ajax({
                url: "{{ url('products/listing/conditions-check-logs/') }}" + "/" + llmId,
                type: "get"
            }).done(function(response) {
                if (response.code = '200') {
                    console.log(response.data);
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
