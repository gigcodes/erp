@extends('layouts.app')
@section('title')
    Compositions 
@endsection
@section('content')
<style type="text/css">
    .small-field { 
        margin-bottom: 0px;
     }
     .small-field-btn {
        padding: 0px 13px;
     }   
</style>
<div class="row">
    <div class="col-md-12">
        <h2 class="page-heading">New Category Reference ({{ $unKnownCategories->total() }})</h2>
    </div>
    <div class="col-md-12">
        <form>
            <div class="form-group col-md-3">
                <input type="search" name="search" class="form-control" value="{{ request('search') }}">
            </div>
            <div class="form-group col-md-2">
                <button type="submit" class="btn btn-secondary">Search</button>
            </div>
        </form>
        <div class="form-group small-field col-md-3">
            <select class="select2 form-control change-list-categories">
                @foreach($categoryAll as $cat)
                    <option value="{{ $cat['id'] }}">{{ $cat['value'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-4">
            <button type="button" class="btn btn-secondary update-category-selected col-md-3">Update</button>
        </div>
    </div>
    <div class="col-md-12 mt-5">
        <table class="table table-bordered">
            <tr>
                <th width="10%"><input type="checkbox" class="check-all-btn">&nbsp;SN</th>
                <th width="30%">Category</th>
                <th width="10%">Count</th>
                <th width="40%">Erp Category</th>
               <!--  <th width="20%">Action</th> -->
            </tr>
            <?php $count = 1; ?>
            {{-- @dd($unKnownCategories->items()); --}}
            @foreach($unKnownCategories as $unKnownCategory)
                @if($unKnownCategory != '')
                    <?php 
                        //getting name 
                        $nameArray  = explode('/',$unKnownCategory);
                        $searchArray  = explode(' ',$unKnownCategory);
                        $name = end($nameArray);

                    ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="categories[]" value="{{ $unKnownCategory }}" class="categories-checkbox">&nbsp;{{ $count }}
                        </td>
                        
                        <td>
                            <span class="call-used-product"  data-type="name">{{ $unKnownCategory }}</span> <button type="button" class="btn btn-image add-list-compostion" data-name="{{ $unKnownCategory }}" ><img src="/images/add.png"></button>
                        </td>
                        
                        <td>
                            {{ \App\Category::ScrapedProducts($searchArray) }}
                        </td>

                        <td>
                            <select class="select2 form-control change-list-category" data-name="{{ $name }}" data-whole="{{ $unKnownCategory }}">
                                @foreach($categoryAll as $cat)
                                    <option value="{{ $cat['id'] }}">{{ $cat['value'] }}</option>
                                @endforeach
                            </select>
                       </td>
                    </tr>
                    <?php $count++; ?>
                @endif
            @endforeach
        </table>
        {{ $unKnownCategories->appends(request()->except('page')) }}
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal show-listing-exe-records" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
    </div>  
</div>
@section('scripts')
    <script type="text/javascript">
            $(".select2").select2({"tags" : true});

            $(document).on("click",".call-used-product",function() {
                var $this = $(this);
                $.ajax({
                    type: 'GET',
                    url: '/compositions/'+$this.data("id")+'/used-products',
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: "json"
                }).done(function (response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        if(response.html != "") {
                            $(".show-listing-exe-records").find('.modal-dialog').html(response.html);
                            $(".show-listing-exe-records").modal('show');
                        }else{
                            toastr['error']('Sorry no product founds', 'error');
                        }
                    }
                }).fail(function (response) {
                    $("#loading-image").hide();
                    toastr['error']('Sorry no product founds', 'error');
                });
            });

            $(document).on("change",".change-list-category",function() {
                var $this = $(this);
                var oldCatid = {{ $unKnownCategoryId }};

                $.ajax({
                    type: 'POST',
                    url: '/category/references/affected-product-new',
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                    data: {
                        _token: "{{ csrf_token() }}",
                        'cat_name' : $this.data("name"),
                        'new_cat_id' : $this.val(),
                        'old_cat_id' : oldCatid,
                        'wholeString': $this.data("whole"),
                    },
                    dataType: "json"
                }).done(function (response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        if(response.html != "") {
                            $(".show-listing-exe-records").find('.modal-dialog').html(response.html);
                            $(".show-listing-exe-records").modal('show');
                        }else{
                            //toastr['error']('Sorry no product founds', 'error');
                        }
                    }
                }).fail(function (response) {
                    $("#loading-image").hide();
                    console.log("Sorry, something went wrong");
                });
            });

            $(document).on("click",".btn-change-composition",function() {
                var $this = $(this);
                 var oldCatid = {{ $unKnownCategoryId }};
                $.ajax({
                    type: 'POST',
                    url: '/category/references/update-category',
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                    data: {
                        _token: "{{ csrf_token() }}",
                        'old_cat_id' : oldCatid,
                        'new_cat_id' : $this.data("to"),
                        'cat_name' : $this.data("from"),
                        'with_product':$this.data('with-product'),
                        'wholeString': $this.data("whole"),
                    },
                    dataType: "json"
                }).done(function (response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        if(response.html != "") {
                            toastr['success'](response.message, 'success');
                        }else{
                            toastr['error']('Sorry, something went wrong', 'error');
                        }
                        $(".show-listing-exe-records").modal('hide');
                    }
                }).fail(function (response) {
                    $("#loading-image").hide();
                    toastr['error']('Sorry, something went wrong', 'error');
                    $(".show-listing-exe-records").modal('hide');
                });
            });

            $(document).on("click",".add-list-compostion",function() {
                var $this = $(this);
                id = $this.data("id");
                to = $('#select'+id).val()
                console.log(to)
                $.ajax({
                    type: 'GET',
                    url: '/compositions/affected-product',
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                    data: {
                        _token: "{{ csrf_token() }}",
                        from : $this.data("name"),
                        to : $this.data("name"),
                    },
                    dataType: "json"
                }).done(function (response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        if(response.html != "") {
                            $(".show-listing-exe-records").find('.modal-dialog').html(response.html);
                            $(".show-listing-exe-records").modal('show');
                        }else{
                            //toastr['error']('Sorry no product founds', 'error');
                        }
                    }
                }).fail(function (response) {
                    $("#loading-image").hide();
                    console.log("Sorry, something went wrong");
                });
            });

            $(document).on("click",".check-all-btn",function() {
                $(".categories-checkbox").trigger("click");
            });


            $(document).on('click','.update-category-selected',function() {
                var changeto = $(".change-list-categories").val();
                var changesFrom = $(".categories-checkbox:checked");
                var ids = [];
                $.each(changesFrom,function(k,v) {
                    ids.push($(v).val());
                });
                var oldCatid = {{ $unKnownCategoryId }};
                $.ajax({
                    type: 'POST',
                    url: '/category/references/update-multiple-category',
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                    data: {
                        _token: "{{ csrf_token() }}",
                        from : ids,
                        to : changeto,
                        old_cat_id: oldCatid
                    },
                    dataType: "json"
                }).done(function (response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        if(response.html != "") {
                            toastr['success'](response.message, 'success');
                            location.reload();
                        }else{
                            toastr['error']('Sorry, something went wrong', 'error');
                        }
                        $(".show-listing-exe-records").modal('hide');
                    }
                }).fail(function (response) {
                    $("#loading-image").hide();
                    toastr['error']('Sorry, something went wrong', 'error');
                    $(".show-listing-exe-records").modal('hide');
                });

            });

    </script>
@endsection
@endsection