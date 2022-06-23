@extends('layouts.app')

@section('title', 'Post man Request')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <h2 class="page-heading">Big Query</h2>
          </div>

          <div class="col-12 mb-3">
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <!-- <a title="add new domain" class="btn btn-secondary add-new-btn">+</a> -->
            </div>
        </div>
    </div>
    <div class="pull-left">
      <form class="form-inline" action="/google/bigData/search" method="GET">
        <div class="col">
          <div class="form-group">
            <div class="input-group">
              <input type="text" placeholder="project id" class="form-control" name="project_id" value="{{old('project_id')}}">
            </div>
          </div>
        </div>
        <div class="col">
          <div class="form-group">
            <div class="input-group">
              <input type="text" placeholder="Enter Platform" class="form-control" name="platform" value="">
            </div>
          </div>
        </div>
        <div class="col">
          <div class="form-group">
            <div class="input-group">
              <input type="text" placeholder="Enter Event ID" class="form-control" name="event_id" value="">
            </div>
          </div>
        </div>
        <div class="col">
          <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
          <a href="/google/bigData/bigQuery" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
        </div>
      </form>
    </div>
   
	</br> 
    <div class="infinite-scroll">
	<div class="table-responsive mt-2">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Google project id</th>
            <th>Platform</th>
            <th>Bundle Identifier</th>
            <th>Event ID</th>
            <th>Issue Title</th>
            <th>Issue Subtitle</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
			  @foreach ($bigData as $key => $bigDatar)
            <tr>
            <td>{{$bigDatar->id}}</td>
            <td>{{$bigDatar->google_project_id}}</td>
            <td>{{$bigDatar->platform}}</td>
            <td>{{$bigDatar->bundle_identifier}}</td>
            <td>{{$bigDatar->event_id}}</td>
            <td>{{$bigDatar->issue_title}}</td>
            <td>{{$bigDatar->issue_subtitle}}</td>
            <td>
              <a class="btn delete-bigData-btn"  data-id="{{ $bigDatar->id }}" href="#"><img  data-id="{{ $bigDatar->id }}" src="/images/delete.png" style="cursor: nwse-resize; width: 16px;"></a>
            </td>
            </tr>
            @endforeach
        </tbody>
      </table>
      <div class="text-center">
        {!! $bigData->appends(Request::except('page'))->links() !!}
    </div>
	</div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
   </div>
@endsection





<div id="view-domain" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content ">
        <div id="view-domain-content">
        
        </div>
      </div>
    </div>
</div>

@section('scripts')
  <script type="text/javascript">

    // $('ul.pagination').hide();
    //   $('.infinite-scroll').jscroll({
    //     autoTrigger: true,
    //     // debug: true,
    //     loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
    //     padding: 0,
    //     nextSelector: '.pagination li.active + li a',
    //     contentSelector: 'div.infinite-scroll',
    //     callback: function () {
    //       $('ul.pagination').first().remove();
    //       $('ul.pagination').hide();
    //     }
		// });
    
    

    $(document).on("click",".delete-bigData-btn",function(e){
        e.preventDefault();
        if (confirm("Are you sure?")) {
          var $this = $(this);
          var id = $this.data('id');
          $.ajax({
            url: "/google/bigData/delete",
            type: "delete",
            headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
            data:{
              id:id
            }
          }).done(function(response) {
            if(response.code = '200') {
              toastr['success'](response.message, 'success'); 
              location.reload();
            } else {
              toastr['error'](response.message, 'error'); 
            }
          }).fail(function(errObj) {
            $('#loading-image').hide();
            $("#addPostman").hide();
            toastr['error'](errObj.message, 'error');
          });
          }
      });
   
    


  </script>
@endsection
