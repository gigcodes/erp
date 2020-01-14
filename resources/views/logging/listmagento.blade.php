@extends('layouts.app')

@section('title', 'Log List Magento')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }

        input {
            width: 100px;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Log List Magento</h2>
            <div class="pull-right">
                <button type="button" class="btn btn-image" onclick="refreshPage()"><img src="/images/resend2.png"/></button>
            </div>

        </div>
    </div>

    @include('partials.flash_messages')

    <div class="col-md-12">
        <div class="panel panel-default">

            <div class="panel-body p-0">
                <form action="{{ route('list.magento.logging') }}" method="GET">
                    <div class="row p-3">
                        <div class="col-md-2">
                            <label for="product_id">Product ID</label>
                            <input type="text" class="form-control" id="product_id" name="product_id" value="{{ old('queue') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="sku">SKU</label>
                            <input type="text" class="form-control" id="sku" name="sku" value="{{ old('sku')}}">
                        </div>
                        <div class="col-md-2">
                            <label for="sku">Brand</label>
                            <input type="text" class="form-control" id="brand" name="brand" value="{{ old('brand')}}">
                        </div>
                        <div class="col-md-2">
                            <label for="sku">Category</label>
                            <input type="text" class="form-control" id="category" name="category" value="{{ old('category')}}">
                        </div>
                        <button class="btn btn-light" id="submit">
                            <span class="fa fa-filter"></span> Filter Results
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <th>Product ID</th>
                            <th>SKU</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Message</th>
                            <th>Date/Time</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach($logListMagentos as $item)
                                <tr>
                                    <td> {{ $item->product_id }} </td>
                                    <td> {{$item->sku}} </td>
                                    <td> {{ $item->brand_name }} </td>
                                    <td> {{$item->category_title}} </td>
                                    <td> {{$item->price}} </td>
                                    <td> {{$item->message}} </td>
                                    <td> 
                                        @if($item->created_at!='')
                                            {{ date('M d, Y',strtotime($item->created_at))}}
                                        @endif
                                    <td>
                                        <button data-toggle="modal" data-target="#update_modal" class="btn btn-primary update_modal" data-id="{{ $item}}"><i class="fa fa-edit"></i></button>
                                    </td>
                                </tr>
                            @endforeach()
                        </tbody>
                    </table>

                    <div class="text-center">
                        {!! $logListMagentos->appends($filters)->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="update_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Product</h4>
                </div>
                <form role="form" action="{{route('product.update.magento')}}" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="update_product_id" id="update_product_id" value="">
                            <div class="form-group col-md-12">
                                <label for="title">Name</label>
                                <input name="name" type="text" class="form-control" id="update_name" value="" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="title">Price</label>
                                <input name="price" type="text" class="form-control" id="update_price" value="" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="title">Price Special</label>
                                <input name="price_eur_special" type="text" class="form-control" id="update_price_eur_special" value="" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="title">Price Discounted</label>
                                <input name="price_eur_discounted" type="text" class="form-control" id="update_price_eur_discounted" value="" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="title">Price INR</label>
                                <input name="price_inr" type="text" class="form-control" id="update_price_inr" value="" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="title">Price Special</label>
                                <input name="price_inr_special" type="text" class="form-control" id="update_price_inr_special" value="" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="title">Price Discounted</label>
                                <input name="price_inr_discounted" type="text" class="form-control" id="update_price_inr_discounted" value="" required>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="title">Composition</label>
                                <input name="composition" type="text" class="form-control" id="update_composition" value="" required>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="title">Measurement Type</label>
                                <input name="measurement_size_type" type="text" class="form-control" id="update_measurement_size_type" value="" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="title">L Measurement</label>
                                <input name="lmeasurement" type="text" class="form-control" id="update_lmeasurement" value="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="title">H Measurement</label>
                                <input name="hmeasurement" type="text" class="form-control" id="update_hmeasurement" value="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="title">D Measurement</label>
                                <input name="dmeasurement" type="text" class="form-control" id="update_dmeasurement" value="">
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary pull-left">Update Product</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#product_id,#sku,#brand,#category').on('blur', function () {
                $.ajax({
                    url: '/logging/list-magento',
                    dataType: "json",
                    data: {
                        product_id: $('#product_id').val(),
                        sku: $('#sku').val(),
                        brand: $('#brand').val(),
                        category: $('#category').val()
                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                }).done(function (data) {
                    $("#loading-image").hide();
                    console.log(data);
                    $("#log-table tbody").empty().html(data.tbody);
                    if (data.links.length > 10) {
                        $('ul.pagination').replaceWith(data.links);
                    } else {
                        $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                    }
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    $("#loading-image").hide();
                    alert(thrownError);
                    alert('No response from server');
                });
            });
        });

        $(document).on("click", ".update_modal", function () {
            var detail = $(this).data('id');

            $("#update_product_id").val(detail['product_id']);
            $("#update_name").val(detail['name']);
            //$("#update_color").val(detail['color']);
            $("#update_price").val(detail['price']);
            $("#update_price_eur_special").val(detail['price_eur_special']);
            $("#update_price_eur_discounted").val(detail['price_eur_discounted']);
            
            $("#update_price_inr").val(detail['price_inr']);
            $("#update_price_inr_special").val(detail['price_inr_special']);
            $("#update_price_inr_discounted").val(detail['price_inr_discounted']);
            
            $("#update_composition").val(detail['composition']);
            $("#update_measurement_size_type").val(detail['measurement_size_type']);
            $("#update_lmeasurement").val(detail['lmeasurement']);
            $("#update_hmeasurement").val(detail['hmeasurement']);
            $("#update_dmeasurement").val(detail['dmeasurement']);
        });
    </script>
@endsection