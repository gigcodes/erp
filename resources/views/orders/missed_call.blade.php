@extends('layouts.app')

@section('content')

    <style>
        td audio {
            height: 30px;
        }

        td {
            padding: 5px 8px 0 !important;

        }

    </style>


    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Missed Call</h2>

        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th style="width: 10%">Mobile Number</th>
                    <th style="width: 10%">Message</th>
                    <th style="width: 20%">Website name</th>
                    <th style="width: 20%">Call Recording</th>
                    <th style="width: 20%">Call Time</th>
                    <th  style="width: 20%">Action</th>
                </tr>
                @foreach ($callBusyMessages['data'] as $key => $callBusyMessage)
                    <tr class="">
                        <td>
                            @if (isset($callBusyMessage['customer_name']))
                                {{ $callBusyMessage['customer_name'] }}
                            @else
                                {{ $callBusyMessage['twilio_call_sid'] }}
                            @endif
                        </td>
                        <td>{{ $callBusyMessage['message'] }}</td>
                        <td>{{ !empty($callBusyMessage['store_website_name']) ? $callBusyMessage['store_website_name'] : ' ' }}
                        </td>
                        <td>
                            <audio src="{{ $callBusyMessage['recording_url'] }}" controls preload="metadata">
                                <p>Alas, your browser doesn't support html5 audio.</p>
                            </audio>
                        </td>
                        <td>{{ $callBusyMessage['created_at'] }}</td>

                        <td>
                            <i class="fa fa-info-circle show-histories" type="button" data-product-id="1709"
                            title="Status Logs" aria-hidden="true" data-id="107" data-name="Status"
                            style="cursor: pointer;" data-call-message-id={{ $callBusyMessage['id'] }}></i>

                            @if (isset($callBusyMessage['customerid']))
                                <a class="btn btn-image"
                                    href="{{ route('customer.show', $callBusyMessage['customerid']) }}"><img
                                        src="/images/view.png" /></a>
                            @endif

                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="order-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Orders</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="show-ordres-table" class="table table-bordered table-hover" style="table-layout:fixed;">
                        <thead>
                            <th>Order id</th>
                            <th>Order type</th>
                            <th>Order status</th>
                            <th>Payment mode</th>
                            <th>Price</th>
                            <th>Currency</th>
                            <th>Order date </th>
                            {{-- <th>Created At</th> --}}
                        </thead>
                        <tbody class="show-ordres-body">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>



    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('audio').on("play", function(me) {
                $('audio').each(function(i, e) {
                    if (e !== me.currentTarget) {
                        this.pause();
                    }
                });
            });
        })


        let customer_id = null;

        $(document).on('click', '.show-histories', function() {

            customer_id = $(this).data("call-message-id")

            $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/order/missed-calls/orders/" +customer_id,
                   
                })
                .done(function(response) {
                    let html = null
                    if (response.length) {

                        response.forEach((element) => {
                            console.log(element)
                            const final_html = `
                                  <tr>
                                    <td style="word-break: break-word;">${element.order_id  ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.order_type ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.order_status ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.payment_mode ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.price ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.currency ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.order_date ?? '-'}</td>
                                </tr>
                                `
                            html += final_html

                        })

                        $('.show-ordres-body').html(html)
                    }

                });
            $('#order-details').modal('show')
        })
    </script>

@endsection
