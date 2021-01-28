@extends('layouts.app')

@section('large_content')

@section('styles')

<style type="text/css">
    #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
	.nav-item a{
		color:#555;
	}
			
	a.btn-image{
		padding:2px 2px;
	}
	.text-nowrap{
		white-space:nowrap;
	}
	.search-rows .btn-image img{
		width: 12px!important;
	}
	.search-rows .make-remark
	{
		border: none;
		background: none
	}
</style>
@endsection
<div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
<div class="row">
	<div class="col-12">
		<h2 class="page-heading">Emails List</h2>
	</div>
</div>
@if ($message = Session::get('success'))
<div class="alert alert-success">
	<p>{{ $message }}</p>
</div>
@endif
<div class="row">
	<div class="col-lg-12 margin-tb">
		<div class="pull-right mt-3">
			<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#statusModel">Create Status</button>
			<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#createEmailCategorytModal">Create Category</button>
		</div>
	</div>   
  <div class="col-md-12">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item active">
              <a class="nav-link" id="read-tab" data-toggle="tab" href="#read" role="tab" aria-controls="read" aria-selected="true" onclick="load_data('incoming',1)">Read</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" id="unread-tab" data-toggle="tab" href="#unread" role="tab" aria-controls="unread" aria-selected="false" onclick="load_data('incoming',0)">Unread</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" id="sent-tab" data-toggle="tab" href="#sent" role="tab" aria-controls="sent" aria-selected="false" onclick="load_data('outgoing','both')">Sent</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="sent-tab" data-toggle="tab" href="#bin" role="tab" aria-controls="bin" aria-selected="false" onclick="load_data('bin','both')">Trash</a>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="read" role="tabpanel" aria-labelledby="read-tab">
          </div>
          <div class="tab-pane fade" id="unread" role="tabpanel" aria-labelledby="unread-tab">

          </div>
          <div class="tab-pane fade" id="sent" role="tabpanel" aria-labelledby="sent-tab">
          </div>
      </div>
  </div>
</div>
<div class="row">
<div class="col-12 mb-3">
  <div class="pull-left">

      <form class="form-inline" >
        <div class="form-group px-2">
          <input id="term" name="term" type="text" class="form-control"
                 value="<?php if(Request::get('term')) echo Request::get('term'); ?>"
                 placeholder="Search by Keyword">
        </div>
        <!--div class="form-group ml-3">
          <div class='input-group date' id='email-datetime'>
            <input type='text' class="form-control" id="date" name="date" value="{{ isset($date) ? $date : '' }}" />
            <span class="input-group-addon">
            <i class="fa fa-calendar" aria-hidden="true"></i>
            </span>
          </div>
        </div-->
		
		<div class="form-group px-2">
          <input id="sender" name="sender" type="text" class="form-control"
                 value="<?php if(Request::get('sender')) echo Request::get('sender'); ?>"
                 placeholder="Search by Sender">
        </div>
		<div class="form-group px-2">
          <input id="receiver" name="receiver" type="text" class="form-control"
                 value="<?php if(Request::get('receiver')) echo Request::get('receiver'); ?>"
                 placeholder="Search by Receiver">
        </div>
		<div class="form-group px-2">
          <select class="form-control" name="status" id="email_status">
				<option value="">Select Status</option>
				<?php
				foreach ($email_status as $status) { ?>
					<option value="<?php echo $status->id;?>" <?php if($status->id == Request::get('status')) echo "selected"; ?>><?php echo $status->email_status;?></option>
				<?php } 
				?>
			</select>
        </div>
		<div class="form-group px-2">
			<select class="form-control" name="category" id="category">
				<option value="">Select Category</option>
				<?php
				foreach ($email_categories as $category) { ?>
					<option value="<?php echo $category->id;?>" <?php if($category->id == Request::get('category')) echo "selected"; ?>><?php echo $category->category_name;?></option>
				<?php } 
				?>
			</select>
        </div>
        <input type='hidden' class="form-control" id="type" name="type" value="" />
        <input type='hidden' class="form-control" id="seen" name="seen" value="1" />

        <button type="submit" class="btn btn-image ml-3 search-btn"><i class="fa fa-filter" aria-hidden="true"></i></button>
      </form>
  </div>
</div>
</div>
<div class="table-responsive" style="margin-top:20px;">
      <table class="table table-bordered text-nowrap" style="border: 1px solid #ddd;" id="email-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Sender</th>
            <th>Receiver</th>
            <th>mail type</th>
            <th>Subject</th>
            <th>Body</th>
            <th>Status</th>
            <th>Category</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <!-- @foreach ($emails as $key => $email)
            <tr>
              <td>{{ Carbon\Carbon::parse($email->created_at)->format('d-m-Y') }}</td>
              <td>{{ $email->from }}</td>
              <td>{{ $email->to }}</td>
              <td>{{ $email->type }}</td>
              <td>
                {{$email->subject}}
              </td>
              <td>
                {{$email->message}}
              </td>
              <td>
              </td>
            </tr>
          @endforeach -->
          @include('emails.search')
        </tbody>
      </table>
      {{$emails->links()}}
</div>

<div id="replyMail" class="modal fade" role="dialog">
    <div class="modal-dialog  modal-lg ">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Email reply</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id="reply-mail-content">
            </div>
        </div>
    </div>
</div>

<div id="forwardMail" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title">Email forward</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div id="forward-mail-content">
          </div>
      </div>
  </div>
</div>

<div id="viewMail" class="modal fade" role="dialog">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">View Email</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <p><strong>Subject : </strong> <span id="emailSubject"></span> </p>
              <p><strong>Message : </strong> <span id="emailMsg"></span> </p>
            </div>
        </div>
    </div>
</div>


<div id="createEmailCategorytModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Create Email Category</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form action="{{ url('email/category') }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<input type="text" name="category_name" value="{{ old('category_name') }}" class="form-control" placeholder="Category Name">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-secondary">Create</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<div id="statusModel" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Update Email Status/Category</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form action="{{ url('email/update_email') }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<input type="text" name="email_status" value="{{ old('email_status') }}" class="form-control" placeholder="Status">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-secondary">Create</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<div id="UpdateMail" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Email List</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form action="{{ url('email/update_email') }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<input type="hidden" name="email_id" id = "email_id">
						<select class="form-control" name="status" id="email_status">
                            <option value="">Select Status</option>
                            <?php
                            foreach ($email_status as $status) { ?>
                                <option value="<?php echo $status->id;?>"><?php echo $status->email_status;?></option>
                            <?php } 
                            ?>
                        </select>
					</div>
					<div class="form-group">
						<select class="form-control" name="category" id="email_category">
                            <option value="">Select Category</option>
                            <?php
                            foreach ($email_categories as $category) { ?>
                                <option value="<?php echo $category->id;?>"><?php echo $category->category_name;?></option>
                            <?php } 
                            ?>
                        </select>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-secondary">Store</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="excelImporter" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Excel Importer</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
        <div class="modal-body">
              <select name="supplier" class="form-control" id="supplier_excel_import">
                <option value="">Select a supplier</option>
                <option value="birba_excel">Birba</option>
                <option value="brunarosso_excel">Bruna Rosso</option>
                <option value="colognese_excel">Colognese (Dior)</option>
                <option value="cologneseSecond_excel">Colognese (Balenciaga, Chloe, Valentino)</option>
                <option value="cologneseThird_excel">Colognese (Saint Laurent)</option>
                <option value="cologneseFourth_excel">Colognese (SS20 Shoes)</option>
                <option value="distributionet_excel">Distributionet</option>
                <option value="gru_excel">Gruppo Pritelli</option>
                <option value="maxim_gucci_excel">Maxim Gucci</option>
                <option value="ines_excel">Ines</option>
                <option value="le-lunetier_excel">Le Lunetier</option>
                <option value="lidia_excel">Lidia</option>
                <option value="lidiafirst_excel">Lidia (Salvatore)</option>
                <option value="modes_excel">Modes</option>
                <option value="mv1_excel">MV1</option>
                <option value="master">Master</option>
                <option value="tory_excel">Tory Outlet</option>
                <option value="tessabit_excel">Tessabit</option>
                <option value="valenti_excel">Valenti</option>
                <option value="valentisecond_excel">Valenti New Format</option>
                <option value="dna_excel">DNA Excel</option>
              </select>
              <input type="hidden" id="excel_import_email_id">
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary" onclick="importExcel()">Store</button>
            </div>
          </div>
        
      </div>
    </div>
  </div>

@include('partials.modals.remarks')

@endsection
@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
        var searchSuggestions = {!! json_encode(array_values($search_suggestions), true) !!};
        var _parentElement = $("#forwardMail")

        // Limit dropdown to 10 emails and use appenTo to view dropdown on top of modal window.
        var options = {
            source: function (request, response) {
                    var results = $.ui.autocomplete.filter(searchSuggestions, request.term);
                    response(results.slice(0, 10));
                },
            appendTo : _parentElement
        };

        // Following is required to load autocomplete on dynamic DOM
        var selector = '#forward-email';
        $(document).on('keydown.autocomplete', selector, function() {
            $(this).autocomplete(options);
        });

        $(document).ready(function() {
        $('#email-datetime').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        });


    $(document).on('click', '.search-btn', function(e) {
      e.preventDefault();
      get_data();
    });

    function get_data(){
      var term = $("#term").val();
      var date = $("#date").val();
      var type = $("#type").val();
      var seen = $("#seen").val();
      var sender = $("#sender").val();
      var receiver = $("#receiver").val();
      var status = $("#email_status").val();
      var category = $("#category").val();
     console.log(window.url);
        $.ajax({
          url: 'email',
          type: 'get',
          data:{
                term:term,
                date:date,
                type:type,
                seen:seen,
				sender:sender,
				receiver:receiver,
				status:status,
				category:category
            },
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
          $("#loading-image").hide();
            $("#email-table tbody").empty().html(response.tbody);
            if (response.links.length > 5) {
                $('ul.pagination').replaceWith(response.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    }


    $(document).on('click', '.resend-email-btn', function(e) {
      e.preventDefault();
      var $this = $(this);
      var type = $(this).data('type');
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email/resendMail/'+$this.data("id"),
          type: 'post',
          data: {
            type:type
          },
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
          toastr['success'](response.message);
          $("#loading-image").hide();
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    });

    $(document).on('click', '.reply-email-btn', function(e) {
      e.preventDefault();
      var $this = $(this);
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email/replyMail/'+$this.data("id"),
          type: 'get',
          beforeSend: function () {
              $("#loading-image").show();
          },
        }).done( function(response) {
          $("#loading-image").hide();
          // toastr['success'](response.message);
          $("#reply-mail-content").html(response);
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    });

    $(document).on('click', '.forward-email-btn', function(e) {
      e.preventDefault();
      var $this = $(this);
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email/forwardMail/'+$this.data("id"),
          type: 'get',
            // beforeSend: function () {
            //     $("#loading-image").show();
            // },
        }).done( function(response) {
          $("#forward-mail-content").html(response);
        }).fail(function(errObj) {
          // $("#loading-image").hide();
        });
    });

    $(document).on('click', '.submit-reply', function(e) {
      e.preventDefault();
      var message = $("#reply-message").val();
      var reply_email_id = $("#reply_email_id").val();
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email/replyMail',
          type: 'post',
          data: {
            'message': message,
            'reply_email_id': reply_email_id
          },
          beforeSend: function () {
              $("#loading-image").show();
          },
        }).done( function(response) {
          $("#replyMail").modal('hide');
          $("#loading-image").hide();
          toastr['success'](response.message);
        }).fail(function(errObj) {
          $("#replyMail").modal('hide');
          $("#loading-image").hide();
          toastr['error'](response.errors[0]);

        });
    });

    $(document).on('click', '.submit-forward', function(e) {
      e.preventDefault();
      email = $("#forward-email").val();
      forward_email_id = $("#forward_email_id").val();
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email/forwardMail',
          type: 'post',
          data: {
            email: email,
            forward_email_id: forward_email_id
          },
          beforeSend: function () {
              $("#loading-image").show();
          },
        }).done( function(response) {
          $("#forwardMail").modal('hide');
          $("#loading-image").hide();
          toastr['success'](response.message);

        }).fail(function(errObj) {
          $("#forwardMail").modal('hide');
          $("#loading-image").hide();
          toastr['error'](response.errors[0]);


        });
    });

	$(document).on('click', '.mailupdate', function (e) {
		
		$("#UpdateMail #email_category").val("").trigger('change');
		$("#UpdateMail #email_status").val("").trigger('change');
		
		var email_id = $(this).data('id');
		var status = $(this).data('status');
		var category = $(this).data('category');
		if(category)
		{
			$("#UpdateMail #email_category").val(category).trigger('change');
		}
		if(status)
		{
			$("#UpdateMail #email_status").val(status).trigger('change');
		}
		
		$('#email_id').val(email_id);
	
	});


    $(document).on('click', '.make-remark', function (e) {
            e.preventDefault();

            var email_id = $(this).data('id');

            console.log(email_id)

            $('#add-remark input[name="id"]').val(email_id);
           

            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('email.getremark') }}',
                data: {
                  email_id: email_id
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done(response => {
                var html = '';
                var no = 1;
                $.each(response, function (index, value) {
                    html += '<tr><th scope="row">' + no + '</th><td>' + value.remarks + '</td><td>' + value.user_name + '</td><td>' + moment(value.created_at).format('DD-M H:mm') + '</td></tr>';
                    no++;
                });
                $("#makeRemarkModal").find('#remark-list').html(html);
                $("#loading-image").hide();
            }).fail(function (response) {
              $("#loading-image").hide();
              toastr['error'](response.errors[0]);
            });;
        });

        $('#addRemarkButton').on('click', function () {
            var id = $('#add-remark input[name="id"]').val();
            var remark = $('#add-remark').find('textarea[name="remark"]').val();

            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('email.addRemark') }}',
                data: {
                    id: id,
                    remark: remark
                },
            }).done(response => {
                $('#add-remark').find('textarea[name="remark"]').val('');
                var no = $("#remark-list").find("tr").length + 1;
                html = '<tr><th scope="row">' + no + '</th><td>' + remark + '</td><td>You</td><td>' + moment().format('DD-M H:mm') + '</td></tr>';
                $("#makeRemarkModal").find('#remark-list').append(html);
            }).fail(function (response) {
                alert('Could not fetch remarks');
            });

        });

        $(document).on('click', '.bin-email-btn', function(e) {
          e.preventDefault();
          var $this = $(this);
            $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: '/email/'+$this.data("id"),
              type: 'delete',
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done( function(response) {

              // Delete current row from UI
              $('#'+$this.data("id")+"-email-row").remove()

              $("#loading-image").hide();
              toastr['success'](response.message);
            }).fail(function(errObj) {
              $("#loading-image").hide();
              toastr['error'](response.errors[0]);
            });
        });

    function opnMsg(email) {
      console.log(email);
      $('#emailSubject').html(email.subject);
      $('#emailMsg').html(email.message);

      // Mark email as seen as soon as its opened
      if(email.seen ==0 || email.seen=='0'){
        // Mark email as read
        var $this = $(this);
            $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: '/email/'+email.id+'/mark-as-read',
              type: 'put'
            }).done( function(response) {

            }).fail(function(errObj) {

            });
      }

    }

    function markEmailRead(email_id){

    }

    function load_data(type,seen){
      $('#type').val(type);
      $('#seen').val(seen);

      get_data();
    }

    function excelImporter(id) {
        $('#excel_import_email_id').val(id)
        $('#excelImporter').modal('toggle');
    }

    function importExcel() {
        id = $('#excel_import_email_id').val()
        supplier = $('#supplier_excel_import option:selected').val()
        if(supplier){
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              data : {
                supplier,
                id
              },
              url: '/email/'+id+'/excel-import',
              type: 'post'
            }).done( function(response) {
              $('#excelImporter').modal('toggle');
              toastr['success'](response.message);
            }).fail(function(errObj) {
              $('#excelImporter').modal('toggle');
              alert('Something went wrong')
            });
        }else{
          alert('Please Select Supplier')
          
        }
        
    }
    </script>


@endsection

