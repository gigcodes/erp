@extends('layouts.app')
@section('content')
<div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Backlink Report</h2>
                </div>
            </div>
        </div>
    </div>
<div class="table-responsive" >	
	<table class="table table-striped table-bordered"> 
		<thead>
			<tr>
			  <th style="width:7%">Backlink report</th>       
			  <th style="width:7%">database</th>       
			  <th style="width:7%">domain</th>       
			  <th style="width:7%">domain_ascore</th>       
			  <th style="width:7%">domain_backlinks_num</th>        
		   </tr>
	   </thead>
	   <tbody id="keywordData">
		@include('seo-tools.partials.backlink-data')
	   </tbody>
	  
	</table>
</div>
<div class="table-responsive" >		
	<table class="table table-striped table-bordered"> 
		<thead>
			<tr>
			  <th style="width:7%">Backlink Anchor report</th>       
			  <th style="width:7%">database</th>       
			  <th style="width:7%">anchor</th>       
			  <th style="width:7%">domains_num</th>       
			  <th style="width:7%">anchor_backlinks_num</th>        
		   </tr>
	   </thead>
	   <tbody id="keywordData">
		@include('seo-tools.partials.backlinkanchor-data')
	   </tbody>
	  
	</table>
</div>
<div class="table-responsive" >		
	<table class="table table-striped table-bordered"> 
		<thead>
			<tr>
			  <th style="width:7%">Backlink Indexed Page</th>       
			  <th style="width:7%">source_url</th>       
			  <th style="width:7%">source_title</th>       
			  <th style="width:7%">response_code</th>
              <th style="width:7%">backlinks_num</th>       
			  <th style="width:7%">domains_num</th>       
			  <th style="width:7%">last_seen</th>
			  <th style="width:7%">external_num</th>       
			  <th style="width:7%">internal_num</th>			  
		   </tr>
	   </thead>
	   <tbody id="keywordData">
		@include('seo-tools.partials.backlinkindexedpage-data')
	   </tbody>
	  
	</table>
</div>
	 


@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
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