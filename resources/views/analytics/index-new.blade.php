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


<div class="row ">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Analytics Data</h2>
    </div>
    <form action="{{route('filteredAnalyticsResults')}}" method="get" class="d-none">
        <div class="" style="">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <select name="dimensionsList" class="form-control">
                                <option value=""> Select dimensions </option>
                                @foreach ($dimensionsList ?? [] as $item)
                                    <option value="{{$item}}" {{ request('dimensionsList') == $item ? 'selected' : ''  }} > {{ $item }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
</div>
@include('partials.flash_messages')

<div class="col-md-12">
    <div id="exTab2" >
        <ul class="nav nav-tabs">
            <li class="{{ request('logs_per_page') || request('crawls_per_page') ? '' : 'active' }}"><a  href="#browser" data-toggle="tab">Platform or Device</a></li>
            <li class="{{ request('logs_per_page') ? 'active' : '' }}"><a href="#geoNetworkData" data-toggle="tab">Geo Network</a>
            <li class="{{ request('crawls_per_page') ? 'active' : '' }}"><a href="#usersData" data-toggle="tab">Users</a>
            {{-- <li class="{{ request('crawls_per_page') ? 'active' : '' }}"><a href="#userType" data-toggle="tab">User Type</a> --}}
            <li class="{{ request('crawls_per_page') ? 'active' : '' }}"><a href="#pageTrackingData" data-toggle="tab">Page Tracking</a>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content" >
    <div class="tab-pane active" id="browser"> 
        <div class="container-fluid">
            <div class="table-responsive ">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Website</th>
                            <th scope="col" class="text-center">Browser</th>
                            <th class="text-center">Opreation system</th>
                            <th scope="col" class="text-center">Session</th>
                            <th scope="col" class="text-center">Created at</th>
                            
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($PlatformDeviceData as $key => $item)
                        <tr>
                            <td>{{ $item['website'] }}</td>
                            <td>{{ $item['browser'] }}</td>
                            <td width="10%">{{ $item['os'] }}</td>
                            <td>{{ $item['session'] }}</td>
                            <td>{{$item['created_at']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- <div class="col-md-12 text-center">
                    {!! $data->links() !!}
                </div> --}}
            </div>
        </div>
    </div>

    <div class="tab-pane" id="geoNetworkData"> 
        <div class="container-fluid">
            <div class="table-responsive ">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Website</th>
                            <th scope="col" class="text-center">Country</th>
                            <th class="text-center">country ISO Code</th>
                            <th scope="col" class="text-center">Sessions</th>
                            <th scope="col" class="text-center">Created at</th>
                            
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($geoNetworkData as $key => $item)
                        <tr>
                            <td>{{ $item['website'] }}</td>
                            <td>{{ $item['country'] }}</td>
                            <td width="10%">{{ $item['iso_code'] }}</td>
                            <td>{{ $item['session'] }}</td>
                            <td>{{$item['created_at']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- <div class="col-md-12 text-center">
                    {!! $data->links() !!}
                </div> --}}
            </div>
        </div>
    </div>

    <div class="tab-pane" id="usersData"> 
        <div class="container-fluid">
            <div class="table-responsive ">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Website</th>
                            <th scope="col" class="text-center">User type</th>
                            <th scope="col" class="text-center">Sessions</th>
                            <th scope="col" class="text-center">Created at</th>
                            
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($usersData as $key => $item)
                        <tr>
                            <td>{{ $item['website'] }}</td>
                            <td>{{ $item['user_type'] }}</td>
                            <td width="10%">{{ $item['session'] }}</td>
                            <td>{{$item['created_at']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- <div class="col-md-12 text-center">
                    {!! $data->links() !!}
                </div> --}}
            </div>
        </div>
    </div>

    <div class="tab-pane " id="userType"> 

    </div>

    <div class="tab-pane " id="pageTrackingData"> 
        <div class="container-fluid">
            <div class="">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Website</th>
                            <th scope="" class="text-center">Page</th>
                            <th class="text-center">Avg time page</th>
                            <th scope="col" class="text-center">Page views</th>
                            <th scope="col" class="text-center">Unique page views</th>
                            <th scope="col" class="text-center">Exit rate</th>
                            <th scope="col" class="text-center">Entrances</th>
                            <th scope="col" class="text-center">Entrance Rate</th>
                            <th scope="col" class="text-center">Created at</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($pageTrackingData as $key => $item)
                        <tr>
                            <td>{{ $item['website'] }}</td>
                            <td width="5%" title="{{$item['page']}}">{{ substr($item['page'], 0, 30)}}</td>
                            <td>{{ $item['avg_time_page'] }}</td>
                            <td width="10%">{{ $item['page_views'] }}</td>
                            <td>{{ $item['unique_page_views'] }}</td>
                            <td>{{ $item['exit_rate'] }}</td>
                            <td>{{ $item['entrances'] }}</td>
                            <td>{{ $item['entrance_rate'] }}</td>
                            <td>{{$item['created_at']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- <div class="col-md-12 text-center">
                    {!! $data->links() !!}
                </div> --}}
            </div>
        </div>
    </div>
</div>
<div class="row mt-5">
    <div class="container-fluid">
        
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