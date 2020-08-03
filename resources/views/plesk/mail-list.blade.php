@extends('layouts.app')

@section('title', 'Plesk mail accounts')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <h2 class="page-heading">Mail accounts</h2>
          </div>
          <div class="col-12 mb-3">
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary add-new-btn" href="{{ route('plesk.domains') }}">Back</a>
            </div>
        </div>
    </div>
    @include('partials.flash_messages')
    
	</br> 
    <div class="infinite-scroll">
	<div class="table-responsive mt-2">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Name</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
			  @foreach ($mailAccount as $key => $account)
            <tr>
            <td>{{$account['name']}}</td>
            <td>
            <a  class="btn btn-xs btn-secondary delete-mail-ac" data-name="{{$account['name']}}" data-id="{{$id}}"><i class="fa fa-trash" aria-hidden="true"></i> </button>
            </td>
            </tr>
            @endforeach
        </tbody>
      </table>

	</div>
    </div>

    <script>
        $(document).ready(function () {
          $(document).on('click', '.delete-mail-ac', function () {
            name = $(this).data('name');
            id = $(this).data('id');
            if(window.confirm("Are you sure ?")) {
              $.ajax({
                url: "/plesk/domains/mail/delete/"+id,
                type: 'POST',
                data: {
                  name : name,
                  "_token": "{{csrf_token()}}"
                },
                success: function (response) {
                  console.log(response);
                    toastr['success'](response.message, 'success');
                    location.reload();
                },
                error: function (error) {
                  console.log(error);
                  toastr['error']('error', 'error');
                }
            });
            }
            });

        });

        </script>
   
@endsection
