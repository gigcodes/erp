@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="col-md-12">
    <h4 class="page-heading">Google Keywords (<span id="adsgroup_count">{{$totalNumEntries}}</span>) for {{@$ad_group_name}} ad group<button class="btn-image" onclick="window.location.href='/google-campaigns/{{$campaignId}}/adgroups'">Back to ad groups</button></h4>
    <div class="pull-left">
        <div class="form-group">
            <div class="row">
                
                <div class="col-md-4">
                    <input name="keyword" type="text" class="form-control" value="{{ isset($keyword) ? $keyword : '' }}" placeholder="Keyword" id="keyword">
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
{{--        <form method="get" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/ad-group-keyword/create">--}}
{{--            <button type="submit" class="btn-sm float-right mb-3">New Keyword</button>--}}
{{--        </form>--}}

        <button type="button" class="float-right custom-button btn mb-3 mr-3" data-toggle="modal" data-target="#new_keyword">New Keyword</button>

        <table class="table table-bordered" id="adsgroup-table">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Keyword</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            @foreach($keywords as $keyword)
                <tr>
                    <td>{{$keyword->id}}</td>
                    <td>{{$keyword->keyword}}</td>
                    <td>{{$keyword->created_at}}</td>
                    <td>
                    <div class="d-flex justify-content-between">
                        {!! Form::open(['method' => 'DELETE','route' => ['ad-group-keyword.deleteKeyword', $campaignId, $keyword['google_adgroup_id'], $keyword['google_keyword_id']],'style'=>'display:inline']) !!}
                        <button type="submit" class="btn-image"><img src="/images/delete.png"></button>
                        {!! Form::close() !!}
                    </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    {{ $keywords->links() }}
    </div>

    <div class="modal fade" id="new_keyword" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="container">
                    <div class="page-header" style="width: 69%">
                        <h2>Create Keyword Assign</h2>
                    </div>
                    <form method="POST" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/ad-group-keyword/create" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" name="campaignId" id="campaignId" value="{{$campaignId}}">
                        <div class="form-group row">
                            <label for="scanurl" class="col-sm-2 col-form-label">Url</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control google_ads_keywords" id="scanurl" name="scanurl" placeholder="Enter a URL to scan for keywords">
                                <span id="scanurl-error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="scan_keywords" class="col-sm-2 col-form-label">Keyword</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control google_ads_keywords" id="scan_keywords" name="scan_keywords" placeholder="Enter products or services to advertise">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label">&nbsp;</label>
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-default" id="btnGetKeywords">Get keyword suggestions</button>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="suggested_keywords" class="col-sm-2 col-form-label">Suggested Keywords</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" id="suggested_keywords" name="suggested_keywords" rows="10" placeholder="Enter or paste keywords. You can separate each keyword by commas."></textarea>

                                <span class="text-muted">Note: You can add up to 80 keyword and each keyword character must be less than 80 character.</span><br>

                                @if ($errors->has('suggested_keywords'))
                                    <span class="text-danger">{{$errors->first('suggested_keywords')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-8">
                                <button type="button" class="float-right ml-2" data-dismiss="modal" aria-label="Close">Close</button>
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
        src = '/google-campaigns/{{ $campaignId }}/adgroups/{{$adGroupId}}/ad-group-keyword';
        keyword = $('#keyword').val();

        $.ajax({
            url: src,
            dataType: "json",
            data: {
                keyword : keyword,
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
        src = '/google-campaigns/{{ $campaignId }}/adgroups/{{$adGroupId}}/ad-group-keyword';
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
            $('#keyword').val('');

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
