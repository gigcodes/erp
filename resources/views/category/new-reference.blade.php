@extends('layouts.app')
@section('title')
    Compositions 
@endsection
@section('content')
<style type="text/css">
    .form-inline label {
        display: inline-block;
    }
    .form-control {
        height: 25px !important;
    }
    .small-field { 
        margin-bottom: 0px;
     }
     .small-field-btn {
        padding: 0px 13px;
     }   
</style>
<div class="row">
    <div class="col-md-12">
        <h2 class="page-heading">Category ({{ count($unKnownCategories) }})</h2>
    </div>
    <div class="col-md-6 mt-5">
        {!! Form::open(["class" => "form-inline" , "route" => 'compositions.store',"method" => "POST"]) !!}    
          <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" class="form-control" id="name" placeholder="Enter Name" value="{{ old('name') ? old('name') : request('name') }}"/>
          </div>
          <div class="form-group ml-2">
            <label for="replace_with">Erp Name:</label>
            <input type="text" name="replace_with" class="form-control" placeholder="Enter Erp Name" value="{{ old('replace_with') ? old('replace_with') : request('replace_with') }}" id="replace_with">
          </div>
          <button type="submit" class="btn btn-default ml-2 small-field-btn">Submit</button>
        </form>
    </div>
    <div class="col-md-6 mt-5">
        {!! Form::open(["class" => "form-inline" , "route" => 'compositions.replace',"method" => "POST"]) !!}    
          <div class="form-group">
            <label for="name">Keyword:</label>
            <input type="text" name="name" class="form-control" id="name" placeholder="Enter Name" value=""/>
          </div>
          <div class="form-group ml-2">
            <label for="replace_with">Replace With:</label>
            <input type="text" name="replace_with" class="form-control" placeholder="Enter Erp Name" value="" id="">
          </div>
          <button type="submit" class="btn btn-default ml-2 small-field-btn">Replace</button>
        </form>
    </div>
    <div class="col-md-4 mt-5">
        {!! Form::open(["class" => "form-inline" , "route" => 'compositions.index',"method" => "GET"]) !!}    
          <div class="form-group">
            <input type="text" name="keyword" class="form-control" id="name" placeholder="Enter keyword" value="{{ old('keyword') ? old('keyword') : request('keyword') }}"/>
          </div>
          <div class="form-group ml-2">
            <input type="checkbox" name="with_ref" class="form-control" id="with_ref" @if(request('with_ref') == 1) checked="checked" @endif value="1"/> With Ref
          </div>
          <button type="submit" class="btn btn-default ml-2 small-field-btn"><i class="fa fa-search"></i></button>
        </form>
    </div>
    <div class="col-md-12 mt-5">
        <table class="table table-bordered">
            <tr>
                <th width="10%">SN</th>
                <th width="30%">Composition</th>
                <th width="40%">Erp Composition</th>
                <th width="20%">Action</th>
            </tr>
            <?php $count = 1; ?>
            @foreach($unKnownCategories as $unKnownCategory)
                @if($unKnownCategory != '')
                <tr>
                    <td>{{ $count }}</td>
                    <td><span class="call-used-product"  data-type="name">{{ $unKnownCategory }}</span> <button type="button" class="btn btn-image add-list-compostion" data-name="{{ $unKnownCategory }}" ><img src="/images/add.png"></button></td>
                    <?php 
                        //getting name 
                        $nameArray  = explode('/',$unKnownCategory);
                        $name = end($nameArray);
                    ?>
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
    </script>
@endsection
@endsection