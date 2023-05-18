@extends('layouts.app')
@section('title', 'Google Dialogflow accounts')
@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Google Dialogflow Accounts</h2>
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-secondary pull-right" data-target="#addAccount" data-toggle="modal">+</button>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">

            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="border: 1px solid #ddd;">
                    <thead>
                    <tr>
                        <th style="width:5%;" class="text-center">Sl no</th>
                        <th style="width:15%;" class="text-center">Site</th>
                        <th style="width:20%;" class="text-center">Client Id</th>
                        <th style="width:20%;" class="text-center">Client Secret</th>
                        <th style="width:5%;" class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody class="text-center" style="word-wrap: break-word;">
                    @foreach($google_dialog_accounts as $key => $google_dialog_account)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{ $google_dialog_account->storeWebsite->title }}</td>
                            <td>{{ $google_dialog_account->google_client_id }}</td>
                            <td>{{ $google_dialog_account->google_client_secret }}</td>
                            <td>
                                <div class="d-flex">
                                    <a onclick="editData('{{ $google_dialog_account->id }}')" class="btn btn-sm edit_account"
                                       style="padding:3px;">
                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                    </a>

                                    <a href="{{ route('google-chatbot-accounts.delete', $google_dialog_account->id) }}" data-id="1"
                                       class="btn btn-delete-template"
                                       onclick="return confirm('Are you sure you want to delete this account ?');"
                                       style="padding:3px;">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </a>
                                    @if(!$google_dialog_account->accounts || count($google_dialog_account->accounts) <= 0)
                                        <a href="{{ route('google-chatbot-account.connect', $google_dialog_account->id) }}" data-id="1"
                                           class="btn btn-delete-template"
                                           style="padding:3px;">
                                            Connect
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @if($google_dialog_account->accounts && count($google_dialog_account->accounts) > 0)
                            <tr>
                                <td colspan="5">
                                    <div>
                                        <h5>Connected Accounts</h5>
                                        <table style="width: 95%;margin: 0 auto;">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($google_dialog_account->accounts as $accountKey => $account)
                                                <tr>
                                                    <td>{!! $accountKey + 1 !!}</td>
                                                    <td>{!! $account->google_account !!}</td>
                                                    <td>
                                                        <a class="btn-image" href="{!! route('google-chatbot-account.disconnect', [$account->id]) !!}"
                                                           title="Disconnect"><img src="/images/delete.png"/></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!--Add Account Modal -->
        <div class="modal fade" id="addAccount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Add Google Dialogflow Account</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @include('google-dialogflow._partial.add-google-dialog-account')
                </div>
            </div>
        </div>

        <!--Update Account Modal -->
        <div class="modal fade" id="updateAccount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update Google Dialogflow Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @include('google-dialogflow._partial.update-google-dialog-account')
                </div>
            </div>
        </div>

    </div>
    <script>
        function editData(id) {
            let url = "{{ route('google-chatbot-accounts.get', [":id"]) }}";
            url = url.replace(':id', id);
            $.ajax({
                url,
                type: 'GET',
                success: function (response) {
                    if (!response.status) {
                        toastr["error"](response.message);
                        $('#updateAccount').modal('hide');
                    } else {
                        $('#updateAccount').modal('show');
                        $('#updateAccount-group-form [name="account_id"]').val(id);
                        $('#updateAccount-group-form [name="google_client_id"]').val(response.data.google_client_id);
                        $('#updateAccount-group-form [name="google_client_secret"]').val(response.data.google_client_secret);
                        $('#updateAccount-group-form [name="site_id"]').val(response.data.site_id);
                    }
                }
            })
        }
    </script>
@endsection
