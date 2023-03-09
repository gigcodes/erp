@extends('layouts.app')
@section('favicon' , 'task.png')
@section('styles')
<style type="text/css">
    #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
</style>
@endsection
@section('content')
    <div class="container" style="margin-top: 10px">
        <h4>Google Responsive Display Ads (<span id="ads_count">{{$totalNumEntries}}</span>) for {{$groupname}} AdsGroup <button class="btn-image float-right custom-button" onclick="window.location.href='/google-campaigns/{{$campaignId}}/adgroups';">Back to Ad groups</button></h4>


        <div class="pull-left" style="margin-top:15px;">
            <div class="form-group">
                <div class="row">
                    
                <div class="col-md-2">
                        <input name="headline" type="text" class="form-control" value="{{ isset($headline) ? $headline : '' }}" placeholder="Headline" id="headline">
                    </div>
                    
                    <div class="col-md-2">
                        <input name="business_name" type="text" class="form-control" value="{{ isset($business_name) ? $business_name : '' }}" placeholder="Business name" id="business_name">
                    </div>

                    <div class="col-md-2">
                        <input name="final_url" type="text" class="form-control" value="{{ isset($final_url) ? $final_url : '' }}" placeholder="Final URL" id="final_url">
                    </div>

                    <div class="col-md-2">
                        <select class="browser-default custom-select" id="ads_status" name="ads_status" style="height: auto">
                        <option value="">--Status--</option>
                        <option value="ENABLED">Enabled</option>
                        <option value="PAUSED">Paused</option>
                        {{-- <option value="DISABLED">Disabled</option> --}}
                    </select>

                    </div>

                    <div class="col-md-1">
                        <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="/images/filter.png" /></button>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png" /></button>
                    </div>
                </div>
            </div>
        </div>

    
        <form method="get" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/responsive-display-ad/create">
            <button type="submit" class="float-right mb-3 custom-button" style="margin-top:10px;">Create</button>
        </form>    

        <table class="table table-bordered" id="ads-table">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Headline 1</th>
                <th>Business Name</th>
                <th>Final Url</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
            </thead>

            <tbody>
            @foreach($ads as $ad)
                <tr>
                    <td>{{$ad->id}}</td>
                    <td>{{$ad->headline1}}</td>
                    <td>{{$ad->business_name}}</td>
                    <td>{{$ad->final_url}}</td>
                    <td>{{$ad->status}}</td>
                    <td>{{$ad->created_at}}</td>
                    <td>
                        <div class="d-flex">
                            <a href="{{ route('responsive-display-ad.show', [$campaignId, $adGroupId, $ad['google_ad_id']]) }}" class="btn btn-image text-dark" title="View"><i class="fa fa-eye"></i></a>
                            {!! Form::open(['method' => 'DELETE','route' => ['responsive-display-ad.deleteAd', $campaignId, $adGroupId,$ad['google_ad_id']], 'style'=>'display:inline']) !!}
                                <button type="submit" class="btn btn-image" title="Delete"><img src="/images/delete.png"></button>
                            {!! Form::close() !!}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $ads->links() }}
    </div>
@endsection

@section('scripts')
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript">
    $('.select-multiple').select2({width: '100%'});

    function submitSearch(){
        src = '/google-campaigns/<?php echo $campaignId; ?>/adgroups/<?php echo $adGroupId; ?>/responsive-display-ad';
        headline = $('#headline').val();
        business_name = $('#business_name').val();
        final_url = $('#final_url').val();
        ads_status = $('#ads_status').val();

        $.ajax({
            url: src,
            dataType: "json",
            data: {
                headline : headline,
                business_name :business_name,
                final_url :final_url,
                ads_status :ads_status,
            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#ads-table tbody").empty().html(data.tbody);
            $("#ads_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
        
    }

    function resetSearch(){
        src = '/google-campaigns/<?php echo $campaignId; ?>/adgroups/<?php echo $adGroupId; ?>/responsive-display-ad';
        blank = ''
        $.ajax({
            url: src,
            dataType: "json",
            data: {
               
               blank : blank, 

            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            headline = $('#headline').val('');
            business_name = $('#business_name').val('');
            final_url = $('#final_url').val('');
            ads_status = $('#ads_status').val('');

            $("#ads-table tbody").empty().html(data.tbody);
            $("#ads_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    }
</script>

@endsection
