@extends('layouts.app')

@section('title', 'Log List Magento')

@section("styles")
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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

	th,td{
    word-break: break-word
  	}
	.btn-secondary{
		border: 1px solid #ddd;
		color: #757575;
		background: #fff !important;
		padding: 5px 10px !important;
	}
	.fa-info-circle{
		cursor: pointer;
	}

</style>
@endsection

@section('content')
  <div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
  </div>
  <div class="row">
        <div class="col-lg-12 margin-tb">
             <h2 class="page-heading">Log List Magento ({{ $total_count }})
						
						  <div class="pull-right">
								<button type="button" class="btn btn-xs btn-secondary read_csv_file" data-toggle="modal" data-target="#product-push-infomation-modal">
										Read csv    
								</button>
						</div>
						
						</h2>
                {{-- <div class="pull-right">
                    <button type="button" class="btn btn-image" onclick="refreshPage()"><img src="/images/resend2.png" /></button>
                    <a href="/logging/magento-product-api-call" target="__blank">
                    <button type="button" class="btn btn-image"><img src="/images/details.png" /></button>
                    </a>
                </div> --}}
         </div>


				 <div class="col-10" style="padding-left:0px;padding-bottom:10px;">
					<div>
							<form class="form-inline" action="" method="GET">
									<div class="form-group col-md-2 pd-3">
											
											<input style="width:100%;" name="filter_product_id" type="text" class="form-control" value="{{ isset($_REQUEST['filter_product_id']) ? $_REQUEST['filter_product_id'] : '' }}" placeholder="Product id">
									</div>
									<div class="form-group col-md-3 pd-3">
											<input style="width:100%;" name="filter_product_sku" type="text" class="form-control" value="{{ isset($_REQUEST['filter_product_sku']) ? $_REQUEST['filter_product_sku'] : '' }}" placeholder="SKU">
									</div>
									<div class="form-group col-md-3 pd-3">
                      <select name="filter_product_status" class="form-control">
                          <option value="">Select status</option>

                              @foreach ($dropdownList as $item)
                                  
                              <option value="{{ $item['status'] }}" {{ (isset($_REQUEST['filter_product_status']) &&( $_REQUEST['filter_product_status']==$item['status'] )) ? 'selected' : '' }}>{{ $item['status'] }}</option>
                              @endforeach

                      </select>
                      
									</div>
									<div class="form-group col-md-1 pd-3">
											<button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>

											<a href="{{ route('list.magento.product-push-information') }}" class="fa fa-refresh" aria-hidden="true"></a>
									</div>
							</form>
					</div>
			</div>

				 

      
{{-- <div class="form-group">
	<form action="{{ route('list.magento.product-push-information') }}">
				<input class="form-control" type="text" name="keyword">
				<button>submit</button>
			</form>
				
			</div> --}}

  <div class="col-md-12">
    <div class="panel panel-default">

      <div class="panel-body p-0">

        <div class="table-responsive">
          <table id="magento_list_tbl_895" class="table table-bordered table-hover" style="table-layout:fixed;">
            <thead>
             
							<th>Product Id</th>
							<th>Sku</th>
							<th>Status</th>
							<th>Quantity</th>
							<th>Stock Status</th>
							<th>Action</th>

            </thead>
            <tbody>
              @foreach($logListMagentos as $item)
              
							<tr>

								<td>{{$item ? $item->product_id : '' }}</td>
								<td>{{$item ? $item->sku : '' }}</td>
								<td>{{$item ? $item->status : '' }}</td>
								<td>{{$item ? $item->quantity : '' }}</td>
								<td>{{$item ? $item->stock_status : '' }}</td>

								<td>
									{{-- <button class="show-histories" data-product-id={{$item ? $item->product_id  :''}}>
										<i class="fa fa-eye"></i>
									</button> --}}

									<i class="fa fa-info-circle show-histories" data-product-id={{$item ? $item->product_id  :''}} title="Status Logs" aria-hidden="true" data-id="107" data-name="Status"></i>
								</td>

							</tr>

              @endforeach()
            </tbody>
          </table>

          <div class="text-center">
            {!! $logListMagentos->links() !!}
          </div>
        </div>
      </div>
    </div>
  </div>


 {{-- product information  --}}
  <div class="modal fade" id="product-push-infomation-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel">Upload CSV History</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

            <form action="{{ route('update.magento.product-push-information') }}" method="POST" id="store-product-push-information">
                @csrf
                <label for="cars">Choose a website:</label>
                <div class="form-group">
                    <select class="form-control" name="websites" id="websites">
                    <option value="https://www.sololuxury.com/var/exportcsv/product.csv">sololuxury.com</option>
                    <option value="https://www.suvandnat.com/var/exportcsv/product.csv">suvandnat.com</option>
                    <option value="https://www.Avoir-chic.com/var/exportcsv/product.csv">Avoir-chic.com</option>
                    <option value="https://www.veralusso.com/var/exportcsv/product.csv">veralusso.com</option>
                    <option value="https://www.Brands-labels.com/var/exportcsv/product.csv">Brands-labels.com</option>
                    </select>

                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">File path</label>
                        <input type="url" class="form-control" id="website-path" value="{{ 'https://www.sololuxury.com/var/exportcsv/product.csv'  }}" name="website_url" required>
                      </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn  btn-secondary">Update data</button>
                </div>
            </form>

        </div>

      </div>
    </div>
  </div>

  
	{{-- show histoies modal --}}
	<div class="modal fade" id="show-histories-data-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel">Product History</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
					<table id="show-histories-table" class="table table-bordered table-hover" style="table-layout:fixed;">
            <thead>
							<th>Product Id</th>
							<th>Sku</th>
							<th>Status</th>
							<th>Quantity</th>
							<th>Stock Status</th>
							<th>Created At</th>
            </thead>
            <tbody class="show-histories-body">
            
            </tbody>
          </table>
            </form>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $("#select_date").datepicker({
	  	format: 'yyyy-mm-dd'
	});
    </script>
  <script type="text/javascript">

$(document).on('submit','#store-product-push-information',function(e){
e.preventDefault()
      $.ajax({
      method: "POST",
      url: "{{ route('update.magento.product-push-information') }}",
      data: $(this).serialize()
    })
    .done(function(response) {
      console.log("Data Saved: ", response);

if(response.error){
	toastr['error'](response.error);
}else{
	location.reload()
}


    });
})


$(document).on('change','#websites',function(){

$('#website-path').val($(this).val())


})


$(document).on('click','.show-histories',function(){
	$.ajax({
      url: "/logging/list-magento/product-push-histories/"+ $(this).data('product-id') ,
    })
    .done(function(response) {
				let html = null
				if(response.length){
				response.forEach((element)=>{
					console.log(element)
					const final_html=			`
								<tr>
									<td style="word-break: break-word;">${element.product_id }</td>
									<td style="word-break: break-word;">${element.sku ?? element.old_sku}</td>
									<td style="word-break: break-word;">${element.status ?? element.old_status}</td>
									<td style="word-break: break-word;">${element.quantity ?? element.old_quantity}</td>
									<td style="word-break: break-word;">${element.stock_status ?? element.old_status	}</td>
									<td style="word-break: break-word;">${element.created_at}</td>
							</tr>
							`
					html+=final_html

				})

				$('.show-histories-body').html(html)
			}

		});
		$('#show-histories-data-modal').modal('show')
})


</script>
@if (Session::has('errors'))
  <script>
  toastr["error"]("{{ $errors->first() }}", "Message")
</script>
@endif
@if (Session::has('success'))
  <script>
  toastr["success"]("{{Session::get('success')}}", "Message")
</script>
@endif

@endsection