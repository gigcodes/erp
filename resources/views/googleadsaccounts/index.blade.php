@extends('layouts.app')
@section('title', 'Google Ads Account')
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
<div class="container " style="max-width: 100%;width: 100%;">
    <div class="row">
    <div class="col-md-12 p-0">
    <h4 class="page-heading">Google AdWords Account (<span id="ads_account_count">{{ $totalentries }}</span>)</h4>
    </div>
    </div>
    <div class="pull-left">
        <div class="form-group">
            <div class="row"> 
                <div class="col-md-5">
                    <select class="form-control select-multiple" id="website-select">
                        <option value="">Select Store Website</option>
                        @foreach($store_website as $key => $sw)
                        <option value="{{ $sw->website }}">{{ $sw->website }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <input name="accountname" type="text" class="form-control" value="{{ isset($accountname) ? $accountname : '' }}" placeholder="Account Name" id="accountname">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn mt-0 btn-image" onclick="submitSearch()"><img src="/images/filter.png" /></button>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn mt-0 btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png" /></button>
                </div>
            </div>
        </div>
    </div>

    <form method="get" action="/google-campaigns/ads-account/create">
        <button type="submit" class="float-right custom-button btn  custom-button mb-3">New Account</button>
    </form>
    <button type="button" class="float-right custom-button btn mb-3 mr-3" data-toggle="modal" data-target="#refreshTokenModal">Generate Access/Refresh Token</button>

    <a href="{{ route('googleadslogs.index') }}" class="float-right custom-button btn mb-3 mr-3" >Logs</a>

    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="adsaccount-table">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Account Name</th>
                    <th>Store Website</th>
                    <th>Config-File</th>
                    <th>Notes</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($googleadsaccount as $googleadsac)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$googleadsac->account_name}}</td>
                    <td>{{$googleadsac->store_websites}}</td>
                    <td>{{$googleadsac->config_file_path}}</td>
                    <td>{{$googleadsac->notes}}</td>
                    <td>{{$googleadsac->status}}</td>
                    <td>{{$googleadsac->created_at}}</td>
                    <td>
                        <a href="/google-campaigns/ads-account/update/{{$googleadsac->id}}" class="btn-image"><img src="/images/edit.png"></a>
                        <a href="/google-campaigns?account_id={{$googleadsac->id}}" class="btn btn-sm">create campaign</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $googleadsaccount->links() }}
</div>

<div class="modal fade" id="refreshTokenModal" role="dialog" style="z-index: 3000;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('googleadsaccount.refresh_token') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Generate Access/Refresh Token</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-4 mb-4">
                        <label>Client ID</label>
                    </div>
                    <div class="col-md-8 mb-4">
                        <input type="input" class="form-control" name="client_id" required />
                    </div>
                    <br />
                    <div class="col-md-4 mb-4">
                        <label>Client Secret</label>
                    </div>
                    <div class="col-md-8 mb-4">
                        <input type="input" class="form-control" name="client_secret" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Generate</button>
                </div>
            </form>
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
        src = '/google-campaigns/ads-account'
        accountname = $('#accountname').val();
        website = $('#website-select').val();
        $.ajax({
            url: src,
            dataType: "json",
            data: {
                accountname : accountname,
                website : website,

            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#adsaccount-table tbody").empty().html(data.tbody);
            $("#ads_account_count").text(data.count);
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
        src = '/google-campaigns/ads-account'
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
            $('#accountname').val('');
            $('#website-select').val('');
            $("#adsaccount-table tbody").empty().html(data.tbody);
            $("#ads_account_count").text(data.count);
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
