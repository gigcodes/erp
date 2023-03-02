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
        <h4>Google App Ads (<span id="ads_count">{{$totalNumEntries}}</span>) for {{$groupname}} AdsGroup <button class="btn-image float-right custom-button" onclick="window.location.href='/google-campaigns/{{$campaignId}}/adgroups';">Back to Ad groups</button></h4>


        <div class="pull-left" style="margin-top:15px;">
            <div class="form-group">
                <div class="row">
                    
                    <div class="col-md-4">
                        <input name="headline" type="text" class="form-control" value="{{ isset($headline) ? $headline : '' }}" placeholder="Headline" id="headline">
                    </div>

                    <div class="col-md-4">
                        <select class="browser-default custom-select" id="ads_status" name="ads_status" style="height: auto">
                        <option value="">--Status--</option>
                        <option value="ENABLED">Enabled</option>
                        {{-- <option value="PAUSED">Paused</option> --}}
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

        
        @if(count($ads) == 0)
        <form method="get" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/app-ad/create">
            <button type="submit" class="float-right mb-3 custom-button" style="margin-top:10px;">Create</button>
        </form> 
        @endif   

        <table class="table table-bordered" id="ads-table">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Headline 1</th>
                <th>Headline 2</th>
                <th>Headline 3</th>
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
                    <td>{{$ad->headline2}}</td>
                    <td>{{$ad->headline3}}</td>
                    <td>{{$ad->status}}</td>
                    <td>{{$ad->created_at}}</td>
                    <td>
                        <div class="d-flex">
                            <a href="{{ route('app-ad.show', [$campaignId, $adGroupId, $ad['google_ad_id']]) }}" class="btn btn-image text-dark" title="View"><i class="fa fa-eye"></i></a>
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
        src = '/google-campaigns/<?php echo $campaignId; ?>/adgroups/<?php echo $adGroupId; ?>/app-ad';
        headline = $('#headline').val();
        ads_status = $('#ads_status').val();

        $.ajax({
            url: src,
            dataType: "json",
            data: {
                headline : headline,
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
        src = '/google-campaigns/<?php echo $campaignId; ?>/adgroups/<?php echo $adGroupId; ?>/app-ad';
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
