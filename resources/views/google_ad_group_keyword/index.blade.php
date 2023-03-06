@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container" style="margin-top: 10px">
    <h4>Google Keywords (<span id="adsgroup_count">{{$totalNumEntries}}</span>) for {{@$ad_group_name}} ad group 
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


    <button class="btn-image" onclick="window.location.href='/google-campaigns/{{$campaignId}}/adgroups'">Back to ad groups</button></h4>
        <form method="get" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/ad-group-keyword/create">
            <button type="submit" class="btn-sm float-right mb-3">New Keyword</button>
        </form>
   
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
