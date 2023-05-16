@extends('layouts.app')

@section('title', 'Order Journey')

@section('content')
    <br />
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="col-md-12 pl-3 pr-3">
        <div class="row m-0">
            <div class="col-lg-12 margin-tb p-0">
                <h2 class="page-heading">Order Journey</h2>
            </div>
            <div class="" style="overflow: scroll;">
                <table id="magento_list_tbl_895" class="table table-bordered table-hover">
                    <thead>
                        <th>Order ID</th>
                        @foreach ($orderStatusList as $orderStatus)
                            <td>{{ $orderStatus }}</td>
                        @endforeach
                    </thead>
                    <tbody class="infinite-scroll-pending-inner">
                        @foreach ($orders as $order)
                            <tr>
                                <td> {{ $order->order_id }} </td>
                                <?php $orderStatusHistories = $order->orderStatusHistories->pluck('created_at', 'new_status')->toArray(); ?>
                                @foreach ($orderStatusList as $key => $orderStatus)
                                    <td>
                                        @if (array_key_exists($key, $orderStatusHistories))
                                            {{ $orderStatusHistories[$key] }}
                                        @endif
                                    </td>
                                @endforeach()
                            </tr>
                        @endforeach()
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        /** infinite loader **/
        var isLoading = false;
        var page = 1;
        $(document).ready(function() {
            $(window).scroll(function() {
                if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
                    loadMore();
                }
            });

            function loadMore() {
                if (isLoading)
                    return;
                isLoading = true;
                var $loader = $('.infinite-scroll-products-loader');
                page = page + 1;
                $.ajax({
                    url: "/order/get-order-journey?page=" + page,
                    type: 'GET',
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function(data) {
                        $loader.hide();
                        $('.infinite-scroll-pending-inner').append(data.tbody);
                        isLoading = false;
                        if (data.tbody == "") {
                            isLoading = true;
                        }
                    },
                    error: function() {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            }
        });
        //End load more functionality
    </script>
@endsection
