@extends('layouts.app')

@section('title', 'Magento Users Manager')

@section('content')

<div class="row">
  <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Magento Users Manager</h2>
  </div>
</div>

@include('partials.flash_messages')

@php
    $isAdmin = auth()->user()->isAdmin();
    // $hasSiteDevelopment = auth()->user()->hasRole('Site-development');
    $userId = auth()->user()->id;
    $pagrank = $storeWebsites->perPage() * ($storeWebsites->currentPage() - 1) + 1;
@endphp

<div class="row mb-3">
  <div class="col-xs-12 pl-5">
      <form class="form-search-data">
          <div class="row">
            <div class="col-2 pd-2">
              <div class="form-group username mb-0">
                 {!!Form::select('store_website_id', [""=>"-- Select website --"]+$allStoreWebsites, request('store_website_id') , ['class' => 'form-control form-control-sm'])!!}
              </div>
            </div>
            <div class="col-2 pd-2">
              <div class="form-group username mb-0">
                 {!!Form::text('username', request('username') , ['class' => 'form-control form-control-sm', 'placeholder'=> 'Username'])!!}
              </div>
            </div>
            <div class="col-2 pd-2">
              <div class="form-group username mb-0">
                 {!!Form::select('role', [""=>"-- Select Role --"]+$magentoRoles, request('role') , ['class' => 'form-control form-control-sm'])!!}
              </div>
            </div>
            <div class="col-2 pd-2">
              <div class="form-group status mb-0">
                <button type="submit" class="btn btn-xs mt-1 ml-3"><i class="fa fa-filter"></i></button>
                <a href="/magento-users" class="btn btn-image mt-auto" id=""><img src="{{asset('/images/resend2.png')}}" style="cursor: nwse-resize;"></a>
                &nbsp;&nbsp;
                <button id="addnew" class="btn btn-default">Add User</button>
              </div>
            </div>
          </div>
      </form>
  </div>
</div>

<div class="mt-3 col-md-12">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Sr. No</th>
        <th>Date</th>
        <th>Username</th>
        @if ($isAdmin)
          <th>Password</th>
        @endif
        <th>Website</th>
        <th>Title</th>
        <th>Website Mode</th>
        <th>Admin Panel</th>
        <th>Role</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
      @if (count($storeWebsites) > 0)
        @foreach ( $storeWebsites as $website)
          <tr>
            <td>{{ $pagrank++ }}</td>
            <td>{{ date_format($website->created_at,"Y-m-d") }}</td>
            <td>{{ $website->username }}</td>
            @if ($isAdmin)
              <td>{{ $website->password }}</td>
            @endif
            <td>{{ $website->website }}</td>
            <td>{{ $website->title }}</td>
            <td>{{ $website->website_mode }}</td>
            <td>{{ $website->magento_url }}</td>
            <td></td>
            <td></td>
          </tr>
        @endforeach
      @else
      <tr>
        <td colspan="8">No users found.</td>
      </tr>
      @endif
    </tbody>
  </table>
</div>

<div class="col-md-12 margin-tb text-center">
  {!! $storeWebsites->appends(request()->capture()->except('page', 'pagination') + ['pagination' => true])->render() !!}
</div>

<div id="addNewUser" class="modal fade" role="dialog">
  <div class="modal-dialog  modal-lg ">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add User</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div id="add-user-form">
        form goes here
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).on('click', '#addnew', function (e){
    e.preventDefault();
    $("#addNewUser").modal('show');
  });
</script>
@endsection