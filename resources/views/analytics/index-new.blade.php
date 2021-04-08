@extends('layouts.app')
@section('title', 'Analytics Data')
@section('content')

<style>
    /** only for the body of the table. */
    table.table tbody td {
        padding:5px;
    }
</style>
<!-- COUPON Rule Edit Modal -->
<div class="modal fade" id="fullUrlModal" tabindex="-1" role="dialog" aria-labelledby="couponModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="couponModalLabel">Full URL</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div><strong><p class="url"></p></strong></div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Analytics Data</h2>
    </div>
    <form action="{{route('filteredAnalyticsResults')}}" method="get" class="">
    <div class="" style="">
        {{-- <div class="col-md-4">
                <div class="form-group col-md-6">
                    <input name="start_date" type="date" placeholder="Start Date" class="form-control"
                        value="{{!empty(request()->start_date) ? request()->start_date : ''}}">
                </div>
                <div class="form-group col-md-6">
                    <input name="end_date" type="date" placeholder="End Date" class="form-control"
                        value="{{!empty(request()->end_date) ? request()->end_date : ''}}">
                </div>
        </div> --}}
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <select name="dimensionsList" class="form-control">
                            <option value=""> Select dimensions </option>
                            @foreach ($dimensionsList as $item)
                                <option value="{{$item}}" {{ request('dimensionsList') == $item ? 'selected' : ''  }} > {{ $item }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- <div class="col-md-3">
                    <div class="form-group">
                        <input name="device_os" type="text" placeholder="Device/OS" class="form-control"
                            value="{{!empty(request()->device_os) ? request()->device_os : ''}}" style="width:100%">
                    </div>
                </div> --}}
                <div class="form-group col-md-3 " style="text-align:right">
                    <button class="btn btn-default">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="col-md-12" style="text-align:end">
    <a href="{{ route('test.google.analytics') }}" class="btn btn-secondary">Get new record</a>
</div>
    {{-- <form action="{{route('filteredAnalyticsResults')}}" method="get" class="form-inline float-right">
    <div class="form-group">
        <div class="col-md-4 col-lg-6 col-xl-6">
            <input name="location" type="text" placeholder="City/Country" class="form-control"
                value="{{!empty(request()->location) ? request()->location : ''}}">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4 col-lg-6 col-xl-6">
            <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
        </div>
    </div>
    </form> --}}
</div>
@include('partials.flash_messages')
<div class="row mt-5">
    <div class="container-fluid">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th scope="col" class="text-center">Website</th>
                        <th scope="col" class="text-center">Dimensions</th>
                        <th class="text-center">Name</th>
                        <th scope="col" class="text-center">Value</th>
                        <th scope="col" class="text-center">Created at</th>
                        
                    </tr>
                </thead>
                <tbody>

                    @foreach ($data as $key => $item)
                    <tr>
                        <td>{{ $item['website'] }}</td>
                        <td>{{ $item['dimensions'] }}</td>
                        <td width="10%">{{ $item['dimensions_name'] }}</td>
                        <td>{{ $item['dimensions_value'] }}</td>
                        <td>{{$item['created_at']}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="col-md-12 text-center">
                {!! $data->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
    });
    function displayFullPath(ele){
        let fullpath = $(ele).attr('data-path');
        $('.url').text(fullpath);
        $('#fullUrlModal').modal('show');
    }
</script>
@endsection