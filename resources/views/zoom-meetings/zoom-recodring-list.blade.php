@extends('layouts.app')

@section('title', 'Meeting Records Info')

@section('styles')
<style>
    .update_description{
        margin-top: 0.50rem;
    }
    .float-right-addbtn{
        float: right !important;
        margin-top: 1%;
        margin-right: 0.095rem;
    }
</style>
@endsection
@section('content')
  <br>
      <div class="table-responsive">
        <table class="table table-bordered" id="users-table">
            <thead>
            <tr>
                <th style="width:10%;">No</th>
                <th style="width:20%;">File Name</th>
                <th style="width:50%;">Description</th>
                <th style="width:5%;">Created At</th>
                <th style="width:5%;">Action</th>
            </tr>
            </thead>
            <tbody>
                @php $i=0; 
            $base_url = config('env.APP_URL');
            @endphp

            @foreach ($zoomRecordings as $key => $metting)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $metting->file_name }}</td>
                    <td><textarea name="description" class="form-control description" placeholder="Description" style="height: 90px;width:70%;">{{ $metting->description }}</textarea>
                    <button class="btn btn-secondary btn-xs update_description" data-id="{{ $metting->id }}">Update</button>
                    </td>
                    <td>{{ $metting->created_at->format('Y-m-d') }}</td>                    <td>
                        <a class="btn btn-secondary mx-3" href="{{ route('meeting.download.file', ['id' =>$metting->id]) }}" title="CSV Downlaod"><i class="fa fa-download"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection

@section('scripts')
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).on("click", ".update_description", function(){
        var meetingId = $(this).attr('data-id');
        var description = $(this).parents('td').find('.description').val();
        if(description != ''){
            $.ajax({
                type: "POST",
                url: "{{ route('meeting.description.update') }}",
                data: {'_token': "{{ csrf_token() }}",id:meetingId,description:description},
                success: function(response) {
                  if(response.code == 200){
                    toastr['success'](response.message, 'success');
                  } else {
                    toastr['error'](response.message, 'error');
                  }              
                }
            });
        } else {
            toastr['success'](response.message, 'success');
        }
    });
    
</script>

@endsection
