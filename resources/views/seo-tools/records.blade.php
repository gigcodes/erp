@extends('layouts.app')
@section('content')
<div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Domain Report</h2>
                </div>
            </div>
        </div>
    </div>
<div class="table-responsive" >	
	<table class="table table-striped table-bordered"> 
		<thead>
			<tr>
			  <th style="width:7%">Domain report</th>       
			  <th style="width:7%">
			  <select id="keyword_type" class=""><option value="organic" selected>Organic</option><option value="paid">Paid</option></select>
			  </th>       
			  <th style="width:7%">Keyword</th>       
			  <th style="width:7%">Position</th>       
			  <th style="width:7%">Previous Position</th>       
			  <th style="width:7%">Position Difference</th>       
			  <th style="width:7%">Search Volume</th>       
			  <th style="width:7%">CPC</th>       
			  <th style="width:7%">Url</th>       
			  <th style="width:7%">Traffic (%)</th>       
			  <th style="width:7%">Traffic Cost (%)</th>       
			  <th style="width:7%">Competition</th>       
			  <th style="width:7%">Number of Results</th>       
			  <th style="width:7%">Trends</th>       
		   </tr>
	   </thead>
	   <tbody id="keywordData">
		@include('seo-tools.partials.domain-data')
	   </tbody>
	  
	</table>
</div>
<div class="table-responsive" >	
	<table class="table table-striped table-bordered"> 
		<thead>
			<tr>
			  <th style="width:7%">Domain Organic Page report</th>       
			  <th style="width:7%">Url</th>       
			  <th style="width:7%">Number_of_Keywords</th>       
			  <th style="width:7%">Traffic</th>       
			  <th style="width:7%">Traffic (%)</th>           
		   </tr>
	   </thead>
	   <tbody id="keywordData">
		@include('seo-tools.partials.domain-organic-page')
	   </tbody>
	  
	</table>
</div>
<div class="table-responsive" >	
	<table class="table table-striped table-bordered"> 
		<thead>
			<tr>
			  <th style="width:7%">Domain Landing Page report</th>       
			  <th style="width:7%">target_url</th>       
			  <th style="width:7%">first_seen</th>       
			  <th style="width:7%">last_seen</th>       
			  <th style="width:7%">times_seen</th>
              <th style="width:7%">ads_count</th>			  
		   </tr>
	   </thead>
	   <tbody id="keywordData">
		@include('seo-tools.partials.domain-landing-page')
	   </tbody>
	  
	</table>
</div>	 
<!-- Model Add Seo tool START -->
    <div id="compModal" class="modal fade in" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" style="padding: 0 10px 10px">
                <div class="modal-header">
                    <h3>Competitors Analysis</h3>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="table-responsive" >	
					<table class="table table-striped table-bordered"> 
						<thead>
							<tr>
						     <th>Domain</th>
							 <th>Common Keywords</th>
							 <th>Organic Keywords</th>
							 <th>Organic Traffic</th>
							</tr>
						</thead>
						<tbody>
						 @include('seo-tools.partials.compitetors-analysis-page')
						</tbody>
				    </table>		
				</div>
            </div>
        </div>
    </div>
    <!-- Model Add Seo tool END -->

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
	$('.select2').select2();
	$('#keyword_type').change(function(){ 
            var type = $(this).val();
            $.ajax({
                url : "{{ url('seo/domain-report/2/') }}"+'/'+type,
                type : "GET",
                success : function (data){ 
					$('#keywordData').html(data);              
                },
                error : function (response){

                }
            });
        });
	  $(document).on('click', '.expand-row-msg', function () {
		var name = $(this).data('name');
		var id = $(this).data('id');
		var full = '.expand-row-msg .show-short-'+name+'-'+id;
		var mini ='.expand-row-msg .show-full-'+name+'-'+id;
		$(full).toggleClass('hidden');
		$(mini).toggleClass('hidden');
	  });
    </script>
@endsection