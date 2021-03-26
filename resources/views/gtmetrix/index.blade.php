@extends('layouts.app')

@section('title','GT Metrix')

@section('content')

<div class = "row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">GTMetrix</h2>
    </div>
</div>


@include('partials.flash_messages')
<div class = "row">
    <div class="col-md-10 margin-tb">
        <div class="pull-left cls_filter_box">
            {{-- <form class="form-inline" action="{{ url('instagram/post/create') }}" method="GET">
                <div class="form-group ml-3 cls_filter_inputbox">
                    <input type="text" id="select_date" name="select_date" value="Select Date"  class="form-control">
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <input type="text" name="comm" class="form-control" value="{{request()->get('comm')}}" placeholder="Comment">
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <input type="text" name="tags" class="form-control" value="{{request()->get('tags')}}" placeholder="Hashtags">
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <input type="text" name="loc" class="form-control" value="{{request()->get('loc')}}" placeholder="Location">
                </div>
                <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
            </form>  --}}
        </div>
    </div>  
    <div class="col-md-2 margin-tb">
        <div class="pull-right mt-3">
            <button type="button" class="btn btn-secondary" btn="" btn-success="" btn-block="" btn-publish="" mt-0="" data-toggle="modal" data-target="#setSchedule" title="" data-id="1">Set cron time
                @if ( $cronTime && !empty( $cronTime->val ))
                    ( <small> {{$cronTime->val}} </small> )
                @endif
            </button>
            @if ( $cronStatus && $cronStatus->val == 'start' )
                <a href ="{{ route('gt-metrix.status','stop') }}" onclick="return confirm('Are you sure?')" class  = "btn btn-secondary"> Stop </a>
            @else
                <a href ="{{ route('gt-metrix.status','start') }}" onclick="return confirm('Are you sure?')" class = "btn btn-secondary"> Start </a>
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 margin-tb">
        {{ $list->links() }}
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="panel mt-3 panel-default">
                <table class="table table-bordered table-striped table-responsive">
                    <thead>
                        <tr>
                            <th>Store view id</th>
                            <th>Test id</th>
                            <th>Status</th>
                            <th>Error</th>
                            <th>Website</th>
                            <th>Report URL</th>
                            <th>Html load time</th>
                            <th>Html bytes</th>
                            <th>Page load time</th>
                            <th>Page bytes</th>
                            <th>Page elements</th>
                            <th>Pagespeed score</th>
                            <th>Yslow score</th>
                            <th>Resources</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $key)
                            <tr>
                                <td>{{ $key->store_view_id }}</td>
                                <td>{{ $key->test_id }}</td>
                                <td>{{ $key->status }}</td>
                                <td>{{ $key->error }}</td>
                                <td>{{ $key->website_url }}</td>
                                <td>{{ ($key->report_url) ? '<a href='.$key->report_url.' target="_blank" title="Show report"> Reprot </a>' : '#' }}</td>
                                <td>{{ $key->html_load_time }}</td>
                                <td>{{ $key->html_bytes }}</td>
                                <td>{{ $key->page_load_time }}</td>
                                <td>{{ $key->page_bytes }}</td>
                                <td>{{ $key->page_elements }}</td>
                                <td>{{ $key->pagespeed_score }}</td>
                                <td>{{ $key->yslow_score }}</td>
                                <td>{{ $key->resources }}</td>
                                <td>{{ $key->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $list->links() }}
    </div>
</div>

@include('gtmetrix.setSchedule')
@endsection
    
@section('scripts')

@endsection
