@extends('layouts.app')
@section('title', 'Conditions checked Products')

@section('large_content')
    @include('partials.flash_messages')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2 class="page-heading">Conditions checked Products </h2>
            <form method="get" id="checkconditionForm">
                <div class="row">
                    <div class="col-md-2 mt-3">
                        <input type="text" name="id" class="form-control" id="id" placeholder="Product Id" />
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="text" name="name" class="form-control" id="name"
                            placeholder="Product Name" />
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="text" name="brand" class="form-control" id="brand" placeholder="Brand" />
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="text" name="category" class="form-control" id="category" placeholder="Category" />
                    </div>

                    <div class="col-md-2 mt-3">
                        <input type="text" name="title" class="form-control" id="producttitle"
                            placeholder="Product Title" />
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="text" name="composition" class="form-control" id="composition"
                            placeholder="Composition" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 mt-3">
                        <input type="text" name="color" class="form-control" id="color" placeholder="Color" />
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="text" name="price" class="form-control" id="price" placeholder="Price" />
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="text" name="status" class="form-control" id="status" placeholder="status" />
                    </div>
                    <div class="col-md-4 mt-3">
                        <a class="filterdata" href="#"><i class="fa fa-search"></i></a>
                    </div>
                    <div class="col-md-2 mt-3">
                        <button class="btn btn-secondary float-right push-to-magento">Push to magento</button>
                    </div>
                </div>
            </form>
            <div class="infinite-scroll table-responsive mt-3 infinite-scroll-data appendData">

            </div>
            <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..."
                style="display: none" />
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js"></script>
    <script>
        $(document).ready(function() {
            var formData = new FormData($("#checkconditionForm")[0]);
            $.ajax({
                url: "{{ route('products.magentoConditionsCheck') }}",
                type: 'GET',
                success: function(response) {
                    $(".appendData").append(response);
                }
            })
        })
        
        function commonAjax(field, val) {
            var fieldData = field;
            $.ajax({
                url: "{{ route('products.autocompleteSearch') }}",
                type: 'POST',
                data: {
                    "filedname": field,
                    "value": val,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    $("#"+field).autocomplete({
                        source:response.data,
                        select: function(event, ui) {
                            var url = '/products/listing/conditions-check?'+field+'='+ui.item.value
                            $.ajax({
                                url: url,
                                type: 'GET',
                                success: function(response) {
                                    $(".appendData").html(response);
                                }
                            })
                        }
                    });

                }
            })
        }

        $("input[name='id']").keyup(function() {
            var value = $(this).val();
            commonAjax("id", value);
        })
        $("input[name='name']").keyup(function() {
            var value = $(this).val();
            commonAjax('name', value);
        })
        $("input[name='brand']").keyup(function() {
            var value = $(this).val();
            commonAjax('brand', value);
        })
        $("input[name='category']").keyup(function() {
            var value = $(this).val();
            commonAjax('category', value);
        })
        $("input[name='title']").keyup(function() {
            var value = $(this).val();
            commonAjax('title', value);
        })
        $("input[name='composition']").keyup(function() {
            var value = $(this).val();
            commonAjax('composition', value);
        })
        $("input[name='color']").keyup(function() {
            var value = $(this).val();
            commonAjax('color', value);
        })
        $("input[name='price']").keyup(function() {
            var value = $(this).val();
            commonAjax('price', value);
        })
        $("input[name='status']").keyup(function() {
            var value = $(this).val();
            commonAjax('status', value);
        })


        $(".filterdata").on('click',function(){
            let id = $("#id").val(); 
         
            let compisition = $("#compisition").val();
            let  color = $("#color").val();
            let status = $("#status").val();
            let price = $("#price").val();
            let title = $("#title").val();
            let  name = $("#name").val();


            var url = '/products/listing/conditions-check?id='+id+"&name="+name+"&title="+title+"&composition="+composition+"&color="+color+"&status="+status+"&price="+price;
                            $.ajax({
                                url: url,
                                type: 'GET',
                                success: function(response) {
                                    $(".appendData").html(response);
                                }
                            })
        });



        $(document).on('click', '.push-to-magento', function() {
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
                beforeSend: function() {
                    $(thiss).text('Loading...');
                    $(thiss).html('<i class="fa fa-spinner" aria-hidden="true"></i>');
                }
            }).done(function(response) {
                if (response.code == 500)
                    toastr['error'](response.message, 'Error')
                else
                    toastr['success'](response.message, 'Success')
                $('#product' + id).hide();
            }).fail(function(response) {
                console.log(response);
                thiss.removeClass('fa-spinner').addClass('fa-upload')
                toastr['error']('Internal server error', 'Failure')
                $('#product' + id).hide();
                //alert('Could not update product on magento');
            }));
            $.when.apply($, ajaxes)
                .done(function() {
                    //location.reload();
                });
        });

        function getConditionCheckLog(llmId) {
            $.ajax({
                url: "{{ url('products/listing/conditions-check-logs/') }}" + "/" + llmId,
                type: "get"
            }).done(function(response) {
                if (response.code = '200') {
                    let html = '';
                    $.each(response.data, function(key, val) {
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
