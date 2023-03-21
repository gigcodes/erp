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

{{--    <form method="get" action="{{url('/google-campaigns/ads-account/create')}}">--}}
{{--        <button type="submit" class="float-right custom-button btn  custom-button mb-3">New Account</button>--}}
{{--    </form>--}}
    <button type="button" class="float-right custom-button btn mb-3 mr-3" data-toggle="modal" data-target="#newaccountmodal">New Account</button>

    <button type="button" class="float-right custom-button btn mb-3 mr-3" data-toggle="modal" data-target="#refreshTokenModal">Generate Access/Refresh Token</button>

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
                        <button type="button" onclick="editaccount('{{$googleadsac->id}}')" class="btn-image" data-toggle="modal" data-target="#EditModal"><img src="{{asset('/images/edit.png')}}"></button>

                        @if(Auth::user()->hasRole('Admin'))
                        {!! Form::open(['method' => 'DELETE','route' => ['googleadsaccount.deleteGoogleAdsAccount', $googleadsac->id],'style'=>'display:inline']) !!}
                            <button type="submit" class="btn-image"><img src="/images/delete.png"></button>
                        {!! Form::close() !!}
                        @endif
                        
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
<div class="modal fade" id="newaccountmodal" role="dialog" style="z-index: 3000;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="container">
                <div class="page-header" style="width: 69%">
                    <h4>Create Account</h4>
                </div>
                <form action="{{ route('googleadsaccount.createAdsAccount') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <label for="account_name" class="col-sm-2 col-form-label">Account name</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="account_name" name="account_name" placeholder="Account Name">
                            @if ($errors->has('account_name'))
                                <span class="text-danger">{{$errors->first('account_name')}}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="google_customer_id" class="col-sm-2 col-form-label">Google Customer Id</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="google_customer_id" name="google_customer_id" placeholder="Google Customer Id">
                            @if ($errors->has('google_customer_id'))
                                <span class="text-danger">{{$errors->first('google_customer_id')}}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="store_websites" class="col-sm-2 col-form-label">Store Websites</label>
                        <div class="col-sm-6">
                            <select class="browser-default custom-select" id="store_websites" name="store_websites" style="height: auto">
                                <option value="" selected>---Selecty store websites---</option>
                                @foreach($store_website as $sw)
                                    <option value="{{$sw->website}}">{{$sw->website}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('store_websites'))
                                <span class="text-danger">{{$errors->first('store_websites')}}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="notes" class="col-sm-2 col-form-label">Notes</label>
                        <div class="col-sm-6">
                            <textarea class="form-control" id="notes" name="notes" placeholder="Notes"></textarea>
                            @if ($errors->has('notes'))
                                <span class="text-danger">{{$errors->first('notes')}}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="config_file_path" class="col-sm-2 col-form-label">Config File</label>
                        <div class="col-sm-6">
                            <input type="file" class="form-control" id="config_file_path" name="config_file_path">
                            @if ($errors->has('config_file_path'))
                                <span class="text-danger">{{$errors->first('config_file_path')}}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="status" class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-6">
                            <select class="browser-default custom-select" id="status" name="status" style="height: auto">
                                <option value="ENABLED" selected>ENABLED</option>
                                <option value="DISABLED">DISABLED</option>
                            </select>
                            @if ($errors->has('status'))
                                <span class="text-danger">{{$errors->first('status')}}</span>
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
<div class="modal fade" id="EditModal"  role="dialog" style="z-index: 3000;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="EditModalLabel">Edit Ads Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="/google-campaigns/ads-account/update" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <input type="hidden" id="account_id" name="account_id" placeholder="Account Id" value="">
                    <div class="form-group row">
                        <label for="account_name" class="col-sm-2 col-form-label">Account name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_account_name" name="account_name" placeholder="Account Name" value="">
                            @if ($errors->has('account_name'))
                                <span class="text-danger">{{$errors->first('account_name')}}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="google_customer_id" class="col-sm-2 col-form-label">Google Customer Id</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_google_customer_id" name="google_customer_id" placeholder="Google Customer Id" value="">
                            @if ($errors->has('google_customer_id'))
                                <span class="text-danger">{{$errors->first('google_customer_id')}}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="store_websites" class="col-sm-2 col-form-label">Store Website</label>
                        <div class="col-sm-10">
                            <select class="browser-default custom-select" id="edit_store_websites" name="store_websites" style="height: auto">
                                <option value="" selected>---Selecty store websites---</option>
                                @foreach($store_website as $sw)
                                    <option value="{{$sw->website}}" >{{$sw->website}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('store_websites'))
                                <span class="text-danger">{{$errors->first('store_websites')}}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="notes" class="col-sm-2 col-form-label">Notes</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="edit_notes" name="notes" placeholder="Notes"></textarea>
                            @if ($errors->has('notes'))
                                <span class="text-danger">{{$errors->first('notes')}}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="config_file_path" class="col-sm-2 col-form-label">Config File</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control" id="edit_config_file_path" name="config_file_path">
                            @if ($errors->has('config_file_path'))
                                <span class="text-danger">{{$errors->first('config_file_path')}}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="status" class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <select class="browser-default custom-select" id="edit_status" name="status" style="height: auto">
                                <option value="ENABLED">ENABLED</option>
                                <option value="DISABLED">DISABLED</option>
                            </select>
                            @if ($errors->has('status'))
                                <span class="text-danger">{{$errors->first('status')}}</span>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="float-right ml-2" data-dismiss="modal" aria-label="Close">Close</button>
                    <button type="submit" class="mb-2 float-right">Update</button>
                </form>
            </div>
            <div class="modal-footer">
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

    function editaccount(id) {
        $('#EditModal').hide();
        var url = "{{ route('googleadsaccount.updatePage', ":id") }}";
        url = url.replace(':id', id);
        $.ajax({
            method: "GET",
            url: url,
            dataType: "json",
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function (data) {
                $('#EditModal').show();
                $("#account_id").val(data.id);
                $("#edit_account_name").val(data.account_name);
                $('#edit_google_customer_id').val(data.google_customer_id);
                $('#edit_store_websites').val(data.store_websites);
                $('#edit_notes').val(data.notes);
                $('#edit_config_file_path').val(data.config_file_path);
                $('#edit_status').val(data.status);
            }
        });
    }
</script>

@endsection
