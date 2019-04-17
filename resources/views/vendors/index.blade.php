@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Vendor Info</h2>
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
              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#vendorCreateModal">+</a>
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
            <th>ID</th>
            <th>Name</th>
            <th>Address</th>
            <th>Social handle</th>
            <th>GST</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($vendors as $vendor)
            <tr>
              <td>{{ $vendor->id }}</td>
              <td>
                {{ $vendor->name }}
                <br>
                <span class="text-muted">
                  {{ $vendor->phone }}
                  <br>
                  {{ $vendor->email }}
                </span>
              </td>
              <td>{{ $vendor->address }}</td>
              <td>{{ $vendor->social_handle }}</td>
              <td>{{ $vendor->gst }}</td>
              <td>
                <button type="button" class="btn btn-image edit-vendor" data-toggle="modal" data-target="#vendorEditModal" data-vendor="{{ $vendor }}"><img src="/images/edit.png" /></button>

                {!! Form::open(['method' => 'DELETE','route' => ['vendor.destroy', $vendor->id],'style'=>'display:inline']) !!}
                  <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                {!! Form::close() !!}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! $vendors->appends(Request::except('page'))->links() !!}

    @include('vendors.partials.vendor-modals')

@endsection

@section('scripts')
  <script type="text/javascript">
    $(document).on('click', '.edit-vendor', function() {
      var vendor = $(this).data('vendor');
      var url = "{{ url('vendor') }}/" + vendor.id;

      $('#vendorEditModal form').attr('action', url);
      $('#vendor_name').val(vendor.name);
      $('#vendor_address').val(vendor.address);
      $('#vendor_phone').val(vendor.phone);
      $('#vendor_email').val(vendor.email);
      $('#vendor_social_handle').val(vendor.social_handle);
      $('#vendor_gst').val(vendor.gst);
    });
  </script>
@endsection
