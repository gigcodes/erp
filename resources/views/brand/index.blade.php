@extends('layouts.app')

@section('styles')
<style>
.btn {
    padding: 6px 6px;
}
</style>
@endsection

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
            <a class="btn btn-secondary" data-toggle="collapse" href="#inProgressFilterCount" href="javascript:;">Number of brands per site</a>
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
                <select class="form-control select-multiple" id="brand" data-placeholder="Brands...">
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
    <div class="col-12 mt-1">
        <div class="form-inline">
            <form>
                <div class="form-group">
                    <input type="text" value="{{ request('keyword') }}" name="keyword" id="search_text" class="form-control" placeholder="Enter keyword for search">
                </div>
                <button type="submit" class="btn btn-secondary ml-3">Search</button>
            </form>
        </div>
    </div>
<br>
<?php 
    $bList = \App\Brand::pluck('name','id')->toArray();
?>
<div class="infinite-scroll">
    {!! $brands->links() !!}
    <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th width="300px">Similar Brands</th>
                <th width="160px">Merge Brands</th>
                <th>Magento ID</th>
                <th>Euro to Inr</th>
                <th>Deduction%</th>
                <th width="150px">Segment</th>
                @foreach($category_segments as $category_segment)
                    <th width="150px">{{ $category_segment->name }}</th>
                @endforeach
                <th width="150px">Selling on</th>
                <th width="150px">Action</th>
            </tr>
            @foreach ($brands as $key => $brand)
            <tr>
                <td>{{ $brand->id }}</td>
                <td>{{ $brand->name }}</td>
                <td><?php echo $brand->references; ?></td>
                <td>
                    <div class="form-select">
                        <?php
                        echo Form::select(
                            "merge_brand",
                            ["" => "-- Select Brand --"] + $bList,
                            $brand->brand_segment,
                            ["class" => "form-control merge-brand", "data-brand-id" => $brand->id]
                        ); ?>
                    </div>
                </td>
                <td class="remote-td">{{ $brand->magento_id}}</td>
                <td>{{ $brand->euro_to_inr }}</td>
                <td>{{ $brand->deduction_percentage }}</td>
                <td>
                    <div class="form-select">
                        <?php
                        echo Form::select(
                            "brand_segment",
                            ["" => "-- Select segment --"] + \App\Brand::BRAND_SEGMENT,
                            $brand->brand_segment,
                            ["class" => "form-control change-brand-segment", "data-brand-id" => $brand->id]
                        ); ?>
                    </div>
                </td>
                @foreach($category_segments as $category_segment)
                    <td>
                        @php
                            $category_segment_discount = \DB::table('category_segment_discounts')->where('brand_id', $brand->id)->where('category_segment_id', $category_segment->id)->first();
                        @endphp

                        @if($category_segment_discount)
                            <input type="text" class="form-control" value="{{ $category_segment_discount->amount }}" onchange="store_amount({{ $brand->id }}, {{ $category_segment->id }})"></th>
                        @else
                            <input type="text" class="form-control" value="" onchange="store_amount({{ $brand->id }}, {{ $category_segment->id }})"></th>
                        @endif
                    </td>
                @endforeach
                <td>
                    <div class="form-select">
                        <?php
                        echo Form::select(
                            "attach_brands[]",
                            ["" => "-- Select Website(s) --"] + $storeWebsite,
                            !empty($brand->selling_on) ? explode(",", $brand->selling_on) : [],
                            ["class" => "form-control select-multiple input-attach-brands", "multiple" => true, "data-brand-id" => $brand->id]
                        ); ?>
                    </div>
                </td>
                <td>
                    <a class="btn btn-image" href="{{ route('brand.edit',$brand->id) }}"><img src="/images/edit.png" /></a>
                    {!! Form::open(['method' => 'DELETE','route' => ['brand.destroy',$brand->id],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                    {!! Form::close() !!}
                    <a class="btn btn-image btn-attach-website" href="javascript:;"><i class="fa fa-globe"></i></a>
                    <a class="btn btn-image btn-create-remote" data-id="{{ $brand->id }}" href="javascript:;"><i class="fa fa-check-circle-o"></i></a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script type="text/javascript">
    function store_amount(brand_id, category_segment_id) {
        var amount = $(this.event.target).val();
        $.ajax({
            url: '{{ route('brand.store_category_segment_discount') }}',
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                'brand_id': brand_id,
                'category_segment_id': category_segment_id,
                'amount': amount
            }
        });
    }
    $(".select-multiple").select2();
    $(".select-multiple4").select2({
        tags: true
    });
    
    $('#calculatePriceButton').on('click', function() {
        var price = $('#product_price').val();
        var brand = $('#brand :selected').data('brand');
        var price_inr = Math.round(Math.round(price * brand.euro_to_inr) / 1000) * 1000;
        var price_special = Math.round(Math.round(price_inr - (price_inr * brand.deduction_percentage) / 100) / 1000) * 1000;

        var result = '<strong>INR Price: </strong>' + price_inr + '<br><strong>Special Price: </strong>' + price_special;

        $('#result-container').html(result);
    });

    $('ul.pagination').hide();
    $(function() {
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
            padding: 2500,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {
                $('ul.pagination').first().remove();
                $(".select-multiple").select2();
            }
        });
    });

    $(document).on("change", ".input-attach-brands", function(e) {
        e.preventDefault();
        var brand_id = $(this).data("brand-id"),
            website = $(this).val();
        $.ajax({
            type: 'POST',
            url: "/brand/attach-website",
            data: {
                _token: "{{ csrf_token() }}",
                website: website,
                brand_id: brand_id
            }
        }).done(function(response) {
            if (response.code == 200) {
                toastr['success']('Website Attached successfully', 'success');
            }
        }).fail(function(response) {
            console.log("Could not update successfully");
        });
    });

    $(document).on("change", ".input-similar-brands", function(e) {
        e.preventDefault();
        var brand_id = $(this).data("reference-id");
        var reference = $(this).val();
        $.ajax({
            type: 'POST',
            url: "/brand/update-reference",
            data: {
                _token: "{{ csrf_token() }}",
                reference: reference,
                brand_id: brand_id
            }
        }).done(function(response) {
            if (response.code == 200) {
                toastr['success']('Reference updated successfully', 'success');
            }
        }).fail(function(response) {
            console.log("Could not update successfully");
        });
    });

    $(document).on("change",".merge-brand",function(e){
        var ready = confirm("Are you sure want to merge brand ?");
        if (ready) {
            var brand_id = $(this).data("brand-id");
            var reference = $(this).val();
            $.ajax({
                type: 'POST',
                url: "/brand/merge-brand",
                data: {
                    _token: "{{ csrf_token() }}",
                    from_brand: brand_id,
                    to_brand: reference
                }
            }).done(function(response) {
                if (response.code == 200) {
                    toastr['success']('Brand merged successfully', 'success');
                    location.reload();
                }
            }).fail(function(response) {
                console.log("Could not update successfully");
            });
        }else{
            return false;
        }    
    });
    

    $(document).on("change", ".change-brand-segment", function(e) {
        e.preventDefault();
        var brand_id = $(this).data("brand-id"),
            segment = $(this).val();
        $.ajax({
            type: 'POST',
            url: "/brand/change-segment",
            data: {
                _token: "{{ csrf_token() }}",
                segment: segment,
                brand_id: brand_id
            }
        }).done(function(response) {
            if (response.code == 200) {
                toastr['success']('Brand segment change successfully', 'success');
            }
        }).fail(function(response) {
            console.log("Could not update successfully");
        });
    });



    $(document).on("click", ".btn-create-remote", function(e) {
        e.preventDefault();
        var $this = $(this);
        var ready = confirm("Are you sure want to create remote id ?");
        if (ready) {
            var brandId = $(this).data("id");
            $.ajax({
                type: 'GET',
                url: "/brand/" + brandId + "/create-remote-id",
            }).done(function(response) {
                if (response.code == 200) {
                    $this.closest("tr").find(".remote-td").html(response.data.magento_id);
                    toastr['success'](response.message, 'success');
                } else if (response.code == 500) {
                    toastr['error'](response.message, 'error');
                }
            }).fail(function(response) {
                console.log("Could not update successfully");
            });
        }
    });
</script>
@endsection
