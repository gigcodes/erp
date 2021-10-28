@extends('layouts.app')

@section('favicon' , 'lead.png')

@section('title', 'Lead and Order Pricing')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Lead and Order Pricing</h2>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                  <div class="col-10" style="padding-left:0px;">
                        <div>
                            <form class="form-inline form-search-data" action="{{ route('lead-order.index') }}" method="GET">
                                <div class="form-group col-md-4 pd-3">
                                  <input style="width:100%;" name="term" type="text" class="form-control" value="{{ isset($term) ? $term : '' }}" 
                                         placeholder="Customer Name, Lead or Order Id, Product Id">
                                </div>
                                <div class="form-group col-md-3 pd-3">
                                    <?php echo Form::select("brand_id",["" => "-- Select Brands --"]+$brandList,request('brand_id',[]),["class" => "form-control select2"]); ?>
                                </div>                                
                                <div class="form-group col-md-3 pd-3">
                                    <select class="form-control select2" name="order_or_lead" tabindex="-1" aria-hidden="true">
                                        <option value="">-- Lead Or Order --</option>
                                        <option value="lead" {{ (isset($orderOrLead) && $orderOrLead == 'lead') ? 'selected' : '' }} >Lead</option>
                                        <option value="order" {{ (isset($orderOrLead) && $orderOrLead == 'order') ? 'selected' : '' }} >Order</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-1 pd-3">
                                    <button type="submit" class="btn btn-xs"><i class="fa fa-filter"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="productGrid" id="productGrid">
	<div class="table-responsive-lg">
		<table class="table table-bordered" style="margin-top: 25px">
		<thead>
			<tr>
				<th style="width: 5%">ID</th>
				<th style="width: 17%">Customer</th>
				<th style="width: 8%">Date</th>
				<th style="width: 5%">Prod ID</th>
				<th style="width: 17%">Prod</th>
				<th style="width: 15%">Brand</th>
				<th style="width: 15%">Default Price</th>
				<th style="width: 15%">Segement Discount</th>
				<th style="width: 15%">Duty Price</th>
				<th style="width: 15%">Override Price</th>
				<th style="width: 7%">Prod Price</th>
				<th style="width: 7%">Disc</th>
				<th style="width: 8%">Final Price</th>
				<th style="width: 10%">GMU</th>
			</tr>
			</thead>
			<tbody class="infinite-scroll-api-inner">
			  @include('lead-order.lead-order-item')
			</tbody>
		</table>
	</div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
      $('#order-datetime').datetimepicker({
        format: 'YYYY-MM-DD'
      });
      $(".select2").select2({tags:true});
    });
      /*$(document).on('click', '.pagination a, th a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        getProducts(url);
      });*/
      $(document).on('click', '.check-lead', function() {
        var id = $(this).data('leadid');
        if ($(this).prop('checked') == true) {
          // $(this).data('attached', 1);
          attached_leads.push(id);
        } else {
          var index = attached_leads.indexOf(id);
          // $(this).data('attached', 0);
          attached_leads.splice(index, 1);
        }
        console.log(attached_leads);
      });
    </script>
<script>      
        var isLoading = false;
        var page = 1;
        $(document).ready(function () {
             $(window).scroll(function() {
                if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
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
                    url: "{{url('lead-order')}}?page="+page,
                    type: 'GET',
                    data: $('.form-search-data').serialize(),
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function (data) {
                        
                        $loader.hide();
                        if('' === data.trim())
                            return;
                        $('.infinite-scroll-api-inner').append(data);
                        

                        isLoading = false;
                    },
                    error: function () {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            }            
        });
  </script>    
@endsection
@section('scripts')
  
@endsection
