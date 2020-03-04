@extends('layouts.app')

@section('content')
 <?php
            $query = http_build_query(Request::except('page'));
            $query = url()->current() . (($query == '') ? $query . '?page=' : '?' . $query . '&page=');
            ?>
            <div class="form-group position-fixed hidden-xs hidden-sm" style="top: 50px; left: 20px;">
            Goto :
            <select onchange="location.href = this.value;" class="form-control" id="page-goto">
                @for($i = 1 ; $i <= $brands->lastPage() ; $i++ )
                    <option value="{{ $query.$i }}" {{ ($i == $brands->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
                @endfor
            </select>
    </div> 
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Brand List (<span>{{ $brands->total() }}</span>) </h2>
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('brand.create') }}">+</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="form-inline">
                <div class="form-group">
                    <input type="number" id="product_price" step="0.01" class="form-control" placeholder="Product price">
                </div>

                <div class="form-group ml-3">
                    <select class="form-control select-multiple" id="brand"  data-placeholder="Brands...">
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" data-brand="{{ $brand }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="button" id="calculatePriceButton" class="btn btn-secondary ml-3">Calculate</button>
            </div>

            <div id="result-container">

            </div>
        </div>
    </div>
    <br>
    {!! $brands->links() !!}

    <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Magento ID</th>
                <th>Euro to Inr</th>
                <th>Deduction%</th>
                <th>Segment</th>
                <th>Selling on</th>
                <th width="200px">Action</th>
            </tr>
            @foreach ($brands as $key => $brand)
                <tr>
                    <td>{{ $brand->id }}</td>
                    <td>{{ $brand->name }}</td>
                    <td>{{ $brand->magento_id}}</td>
                    <td>{{ $brand->euro_to_inr }}</td>
                    <td>{{ $brand->deduction_percentage }}</td>
                    <td>{{ $brand->brand_segment }}</td>
                    <td>
                        <div class="form-select">
                            <?php 
                            echo Form::select(
                                "attach_brands[]",
                                ["" => "-- Select Website(s) --"] + $storeWebsite,
                                !empty($brand->selling_on) ? explode(",", $brand->selling_on) : [],
                                ["class" => "form-control select-multiple input-attach-brands" ,"multiple" => true, "data-brand-id" => $brand->id]
                            ); ?>
                        </div>    
                    </td>
                    <td>
                        <a class="btn btn-image" href="{{ route('brand.edit',$brand->id) }}"><img src="/images/edit.png"/></a>
                        {!! Form::open(['method' => 'DELETE','route' => ['brand.destroy',$brand->id],'style'=>'display:inline']) !!}
                        <button type="submit" class="btn btn-image"><img src="/images/delete.png"/></button>
                        {!! Form::close() !!}
                        <a class="btn btn-image btn-attach-website" href="javascript:;"><i class="fa fa-globe"></i></a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

@endsection
@section('scripts')
    <script type="text/javascript">
        $(".select-multiple").select2();
        $('#calculatePriceButton').on('click', function () {
            var price = $('#product_price').val();
            var brand = $('#brand :selected').data('brand');
            var price_inr = Math.round(Math.round(price * brand.euro_to_inr) / 1000) * 1000;
            var price_special = Math.round(Math.round(price_inr - (price_inr * brand.deduction_percentage) / 100) / 1000) * 1000;

            var result = '<strong>INR Price: </strong>' + price_inr + '<br><strong>Special Price: </strong>' + price_special;

            $('#result-container').html(result);
        });


       $(document).on("change",".input-attach-brands",function(e) {
            e.preventDefault();
            console.log($(this));
            var brand_id = $(this).data("brand-id"),
                website  = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: "/brand/attach-website",
                    data: {
                        _token: "{{ csrf_token() }}",
                        website:website,
                        brand_id:brand_id
                    }
              }).done(function(response) {
                if(response.code == 200) {
                    toastr['success']('Website Attached successfully', 'success');   
                }
              }).fail(function(response) {
                 console.log("Could not update successfully");
              });

       }); 
    </script>
@endsection
