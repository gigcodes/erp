@extends('layouts.app')
@section('title', 'Analytics Data')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Analytics Data</h2>
        </div>
        <form action="{{route('filteredAnalyticsResults')}}" method="get" class="form-inline">
            <div class="form-group">
                <div class="col-md-4 col-lg-6 col-xl-6">
                    <input name="date" type="date" class="form-control" value="{{request()->date ?? ''}}">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4 col-lg-6 col-xl-6">
                    <button class="btn btn-default">Filter</button>
                </div>
            </div>
        </form>
    </div>
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Referers</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">User Types</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Visitors</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pills-total-tab" data-toggle="pill" href="#pills-total" role="tab" aria-controls="pills-total" aria-selected="false">Total Visitors & Page Views</a>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade active in" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th rowspan="1" class="text-center">From (URL)</th>
                        <th rowspan="1" class="text-center">Page Views</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach (unserialize($top_referers_ser[0]) as $key => $referer)
                            <tr>
                                <td>{{$referer['url']}}</td>
                                <td class="text-center">{{$referer['pageViews']}}</td>
                            </tr>    
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th rowspan="1" class="text-center">User Type</th>
                        <th rowspan="1" class="text-center">Sessions</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach (unserialize($user_types_ser[0]) as $key => $user)
                            <tr>
                                <td class="text-center">{{$user['type']}}</td>
                                <td class="text-center">{{$user['sessions']}}</td>
                            </tr>    
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th rowspan="2" class="text-center">Date</th>
                        <th rowspan="2" class="text-center">Page Title</th>
                        <th rowspan="2" class="text-center">Page URL</th>
                        <th rowspan="2" class="text-center">Page Views</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($visitors_ser as $key => $page)
                            <tr>
                                <td>{{Carbon\Carbon::parse($page['date'])->format('D M, Y')}}</td>
                                <td>{{$page['pageTitle']}}</td>
                                <td>{{$page['visitors']}}</td>
                                <td>{{$page['pageViews']}}</td>
                            </tr>    
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="pills-total" role="tabpanel" aria-labelledby="pills-total-tab">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th rowspan="2" class="text-center">Date</th>
                        <th rowspan="2" class="text-center">visitors</th>
                        <th rowspan="2" class="text-center">Page Views</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($total_views as $key => $total)
                            <tr>
                                <td class="text-center">{{\Carbon\Carbon::Parse($total['date'])->format('D M, Y')}}</td>
                                <td class="text-center">{{$total['visitors']}}</td>
                                <td class="text-center">{{$total['pageViews']}}</td>
                            </tr>    
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection