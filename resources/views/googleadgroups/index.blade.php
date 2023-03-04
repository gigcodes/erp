@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="col-md-12">
        <h2 class="page-heading">Google AdGroups (<span id="adsgroup_count">{{$totalNumEntries}}</span>) for {{@$campaign_name}} campaign name</h2>

    <div class="pull-left p-0">
        <div class="form-group">
            <div class="row">
                
                <div class="col-md-3 pr-2">
                    <input name="googlegroup_name" type="text" class="form-control" value="{{ isset($googlegroup_name) ? $googlegroup_name : '' }}" placeholder="Group Name" id="googlegroup_name">
                </div>

                <div class="col-md-2 p-0 pr-2">
                    <input name="googlegroup_id" type="text" class="form-control" value="{{ isset($googlegroup_id) ? $googlegroup_id : '' }}" placeholder="Group ID" id="googlegroup_id">
                </div>
                @if(!in_array(@$campaign_channel_type, ["MULTI_CHANNEL"]))
                <div class="col-md-2 p-0 pr-2">
                    <input name="bid" type="text" class="form-control" value="{{ isset($bid) ? $bid : '' }}" placeholder="Bid" id="bid">
                </div>
                @endif

                <div class="col-md-2 p-0">
                    <select class="browser-default custom-select" id="adsgroup_status" name="adsgroup_status" style="height: auto">
                    <option value="">--Status--</option>
                    <option value="ENABLED">Enabled</option>
                    <option value="PAUSED">Paused</option>
                    </select>
                </div>

                <div class="col-md-2 pl-0 mt-1">
                    <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="{{asset('/images/filter.png')}}" /></button>
                    <button type="button" class="btn btn-image pl-0" id="resetFilter" onclick="resetSearch()"><img src="{{asset('/images/resend2.png')}}" /></button>
                </div>
            </div>
        </div>
    </div>


        <button type="button" class="float-right custom-button btn mb-3 mr-3" data-toggle="modal" data-target="#adgroupmodal">New Ad Group</button>
   
        <table class="table table-bordered" id="adsgroup-table">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Ads Group Name</th>
                <th>Google Campaign Id</th>
                <th>Google Adgroupd Id</th>
                @if(!in_array(@$campaign_channel_type, ["MULTI_CHANNEL"]))
                <th>Bid</th>
                @endif
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            @foreach($adGroups as $adGroup)
                <tr>
                    <td>{{$adGroup->id}}</td>
                    <td>{{$adGroup->ad_group_name}}</td>
                    <td>{{$adGroup->adgroup_google_campaign_id}}</td>
                    <td>{{$adGroup->google_adgroup_id}}</td>
                    @if(!in_array(@$campaign_channel_type, ["MULTI_CHANNEL"]))
                    <td>{{$adGroup->bid}}</td>
                    @endif
                    <td>{{$adGroup->status}}</td>
                    <td>{{$adGroup->created_at}}</td>
                    <td>
                    <div class="d-flex">
                        @if(in_array(@$campaign_channel_type, ["DISPLAY"]))
                            <form method="GET" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroup['google_adgroup_id']}}/responsive-display-ad">
                                <button type="submit" class="btn-image">Display Ads</button>
                            </form>
                        @elseif(in_array(@$campaign_channel_type, ["SEARCH"]))
                            <form method="GET" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroup['google_adgroup_id']}}/ads">
                                <button type="submit" class="btn-image">Ads</button>
                            </form>
                        @elseif(in_array(@$campaign_channel_type, ["MULTI_CHANNEL"]))
                            <form method="GET" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroup['google_adgroup_id']}}/app-ad">
                                <button type="submit" class="btn-image">App Ads</button>
                            </form>
                        @endif
                        {!! Form::open(['method' => 'DELETE','route' => ['adgroup.deleteAdGroup',$campaignId,$adGroup['google_adgroup_id']],'style'=>'display:inline']) !!}
                        <button type="submit" class="btn-image"><img src="{{asset('/images/delete.png')}}"></button>
                        {!! Form::close() !!}
                        {!! Form::open(['method' => 'GET','route' => ['adgroup.updatePage',$campaignId,$adGroup['google_adgroup_id']],'style'=>'display:inline']) !!}
                        <button type="submit" class="btn-image ml-2"><img src="{{asset('/images/edit.png')}}"></button>
                        {!! Form::close() !!}
                    </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    {{ $adGroups->links() }}
    </div>

        <div class="modal fade" id="adgroupmodal" role="dialog" style="z-index: 3000;">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="container">
                    <div class="page-header" style="width: 69%">
                        <h2>Create Ad group for {{$campaign_name}}</h2>
                    </div>
                    <form action="{{route('adgroup.createAdGroup',['id'=> $campaignId])}}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <label for="ad-group-name" class="col-sm-2 col-form-label">Ad group name</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="ad-group-name" name="adGroupName" placeholder="Ad group name">
                                @if ($errors->has('adGroupName'))
                                    <span class="text-danger">{{$errors->first('adGroupName')}}</span>
                                @endif
                            </div>
                        </div>
                        @if($campaign_channel_type != "MULTI_CHANNEL")
                            <div class="form-group row">
                                <label for="bid-amount" class="col-sm-2 col-form-label">Bid amount</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="bid-amount" name="microAmount" placeholder="Bid amount">
                                    @if ($errors->has('microAmount'))
                                        <span class="text-danger">{{$errors->first('microAmount')}}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label for="ad-group-status" class="col-sm-2 col-form-label">Ad group status</label>
                            <div class="col-sm-6">
                                <select class="browser-default custom-select" id="ad-group-status" name="adGroupStatus" style="height: auto">
                                    <option value="1" selected>Enabled</option>
                                    <option value="2">Paused</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-8">
                                <button type="submit" class="mb-2 float-right">Create</button>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('scripts')
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript">
    $('.select-multiple').select2({width: '100%'});

    function submitSearch(){
        src = '/google-campaigns/<?php echo $campaignId;  ?>/adgroups';
        googlegroup_name = $('#googlegroup_name').val();
        googlegroup_id = $('#googlegroup_id').val();
        bid = $('#bid').val();
        adsgroup_status = $('#adsgroup_status').val();

        $.ajax({
            url: src,
            dataType: "json",
            data: {
                googlegroup_name : googlegroup_name,
                googlegroup_id :googlegroup_id,
                bid :bid,
                adsgroup_status :adsgroup_status,
            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#adsgroup-table tbody").empty().html(data.tbody);
            $("#adsgroup_count").text(data.count);
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
        src = '/google-campaigns/<?php echo $campaignId;  ?>/adgroups';
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
            $('#googlegroup_name').val('');
            $('#googlegroup_id').val('');
            $('#bid').val('');
            $('#adsgroup_status').val('');
         

            $("#adsgroup-table tbody").empty().html(data.tbody);
            $("#adsgroup_count").text(data.count);
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
