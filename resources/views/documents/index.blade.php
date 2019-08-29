@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Documents Manager</h2>
            <div class="pull-left">
              {{-- <form action="/order/" method="GET">
                  <div class="form-group">
                      <div class="row">
                          <div class="col-md-12">
                              <input name="term" type="text" class="form-control"
                                     value="{{ isset($term) ? $term : '' }}"
                                     placeholder="Search">
                          </div>
                          <div class="col-md-4">
                              <button hidden type="submit" class="btn btn-primary">Submit</button>
                          </div>
                      </div>
                  </div>
              </form> --}}
            </div>
            <div class="pull-right">
              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#documentCreateModal">+</a>

            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Date</th>
            <th>User</th>
            <th>Department</th>
            <th>Document Type</th>
            <th>Category</th>
            <th>Filename</th>
            <th>Actions</th>
            <th>Remarks</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($documents as $document)
            <tr>
              <td>{{ $document->updated_at->format('d.m-Y') }}</td>
              <td>{{ $document->user->name }}</td>
              <td>{{ $document->user->agent_role  }}</td>
              <td>{{ $document->name}}</td>
              <td>{{ $document->category}}</td>
              <td>{{ $document->filename }}</td>
              <td>
                <a href="{{ route('document.download', $document->id) }}" class="btn btn-xs btn-secondary">Download</a>
                <button type="button" class="btn btn-image"><img src="/images/send.png" /></button>
                <button type="button" class="btn btn-image"><img src="/images/customer-email.png" /></button>
                 
                {!! Form::open(['method' => 'DELETE','route' => ['document.destroy', $document->id],'style'=>'display:inline']) !!}
                  
                  <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                  
                {!! Form::close() !!}
                <button type="button" class="btn btn-image"><img src="/images/upload.png" /></button>

                V: {{ $document->version }}
              </td>
              <td><button type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $document->id }}"><img src="/images/remark.png" /></button></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! $documents->appends(Request::except('page'))->links() !!}
       @include('partials.modals.remarks')

    <div id="documentCreateModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

                <!-- Modal content-->
        <div class="modal-content">
          <form action="{{ route('document.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="modal-header">
              <h4 class="modal-title">Store a Document</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <select class="selectpicker form-control" data-live-search="true" data-size="15" name="user_id" title="Choose a User" required>
                    @foreach ($users as $user)
                      <option data-tokens="{{ $user->name }} {{ $user->email }}" value="{{ $user->id }}"  {{ $user->id == old('user_id') ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>

                @if ($errors->has('user_id'))
                  <div class="alert alert-danger">{{$errors->first('user_id')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Document Type:</strong>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>

                @if ($errors->has('name'))
                  <div class="alert alert-danger">{{$errors->first('name')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Document category:</strong>
                 <select class="selectpicker form-control" data-live-search="true" data-size="15" name="category" title="Choose a Category" required>
                    
                      <option value="KYC">KYC</option>
                    
                </select>
                 @if ($errors->has('category'))
                  <div class="alert alert-danger">{{$errors->first('category')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>File:</strong>
                <input type="file" name="file[]" class="form-control" value="" multiple required>

                @if ($errors->has('file'))
                  <div class="alert alert-danger">{{$errors->first('file')}}</div>
                @endif
              </div>

               <div class="form-group">
                <strong>Version:</strong>
                <input type="text" name="version" class="form-control" value="1" required>

                @if ($errors->has('version'))
                  <div class="alert alert-danger">{{$errors->first('version')}}</div>
                @endif
              </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Upload</button>
            </div>
          </form>
        </div>

      </div>
    </div>

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>


  <script type="text/javascript">
       $(".make-remark").on('click', function() {
                   var id = $(this).data('id');
                   $('#add-remark input[name="id"]').val(id);
              });
          $('#addRemarkButton').on('click', function() {

        var id = $('#add-remark input[name="id"]').val();
        var remark = $('#add-remark textarea[name="remark"]').val();

        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route('task.addRemark') }}',
            data: {
              id:id,
              remark:remark,
              module_type: 'document'
            },
        }).done(response => {
         
            alert('Remark Added Success!')
            window.location.reload();
        }).fail(function(response) {
          console.log(response);
        });
      });

  </script>
@endsection
