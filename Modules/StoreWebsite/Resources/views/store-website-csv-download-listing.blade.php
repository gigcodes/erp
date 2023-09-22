@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Store Websites ({{$storeWebsites->total()}})</h2>
		</div>
	</div>
    <div class="mt-3 col-md-12">
        <form action="{{ route('website.search.log.view') }}" method="get" class="search">
            <!-- Form fields go here -->
    
            <div class="col-lg-2 pull-left">
                <!-- Other form elements go here -->
            </div>
    
            <div class="col-lg-2 pull-right"> <!-- This div wraps the "Csv download" button -->
                <button type="button" class="btn btn-secondary csv-download"  onclick="return confirm('{{ __('Are you sure you want to Download') }}')">Csv download
                  </button>
            </div>
        </form>
    </div>
    
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">No</th>
			    	<th width="20%">Websites</th>
			        <th width="10%">File Name</th>
                    <th width="3%">Action</th>

                </tr>
		    	<tbody>
                    @foreach ($storeWebsites as $storeWeb)
                        <tr>
                            <td><input type="checkbox" name="storewebsite" id="storewebsite" value="{{$storeWeb->id}}" data-id="{{ $storeWeb->id }}" data-select="true"></td>
                            <td>{{$storeWeb->title}}</td>
                            @if ($storeWeb->csvFiles->isNotEmpty())
                            <td> {{ $storeWeb->csvFiles->last()->path }}  </td> 
                            @else
                            <td> - </td>
                            @endif
                            <td><button type="button" id="ip_log" class="btn btn-secondary process-magento-csv-btn" title="pullCsvDownlaod" data-id="{{$storeWeb->id}}" onclick="return confirm('{{ __('Are you Want to execute') }}')"> 
                                <i class="fa fa-paper-plane " aria-hidden="true"></i></button>
                                 <a href="{{ route('store-website.push.csv', ['id' => $storeWeb->id]) }}" class="btn btn-secondary">view push csv files</a>
                            </td>
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $storeWebsites->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>
@endsection

@section('scripts')
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() 
	{
        $(document).on('click', '.csv-download', function() {
            var websiteIDS = [];
            var selectedCheckboxes = [];

            $('input[name="storewebsite"]:checked').each(function() {
                var websiteID = $(this).data('id');
                var checkboxValue = $(this).val();

                websiteIDS.push(websiteID);
                selectedCheckboxes.push(checkboxValue);
            });

            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one checkbox.');
                return;
            }

            var website_ids = selectedCheckboxes.join(',');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{route('mulitiple.store-website.listing')}}',
                type: 'POST',
                data: {
                    website_ids: website_ids,
                },
                dataType: 'json',
                    beforeSend: function () {
                        $("#loading-image-preview").show();
                    },
                    success: function (response) {
                        $("#loading-image-preview").hide();
                        toastr["success"](response.message);
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        toastr['error']("Invalid JSON response", 'error');
                    },
                    complete: function () {
                        $("#loading-image-preview").hide();
                    }
            });
        });

        $(document).on("click", ".process-magento-csv-btn", function(e) {
            e.preventDefault();
            var id = $(this).data("id");

            $.ajax({
                url: '{{route('store-website.single.command.run')}}',
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, 
                data: {
                    id: id,
                },
                beforeSend: function() {
                    $('#loading-image').show();
                },
            }).done(function(response) {
                if (response.code == '200') {
                    toastr['success']('Command Run successfully!!!', 'success');
                } else if(response.code == '500') {
                    toastr['error'](response.message, 'error');
                }
                else {
                    toastr['error'](response.message, 'error');
                }
                $('#loading-image').hide();
            }).fail(function(errObj) {
                $('#loading-image').hide();
                    toastr['error']("Invalid JSON response", 'error');

            });
         });

	});
</script> 
@endsection
    