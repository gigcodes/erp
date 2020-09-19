@extends('layouts.app')
@section('title', 'Watson accounts')
@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Watson Accounts</h2>
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
                            <th scope="col" class="text-center">Sl no</th>
                            <th scope="col" class="text-center">Website</th>
                            <th scope="col" class="text-center">Api Key</th>
                            <th scope="col" class="text-center">Instance URL</th>
                            <th scope="col" class="text-center">Is Active</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody class="text-center">
                        @foreach($accounts as $key => $account)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{ $account->storeWebsite->title }}</td>
                                    <td>{{ $account->api_key }}</td>
                                    <td>{{ $account->url }}</td>
                                    <td>{{ $account->is_active ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <a type="button" data-id="{{ $account->id }}" class="btn btn-sm edit_account">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </a>
                                        <a type="button" href="{{ route('twilio-delete-account', $account->id) }}" data-id="1" class="btn btn-delete-template" onclick="return confirm('Are you sure you want to delete this account ?');">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
            </div>
        </div>

        <!--Add Account Modal -->
        <div class="modal fade" id="addAccount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Watson Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" id="submit-watson-account" action="">
                        @csrf
                        <div class="modal-body mb-2">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <label>Website</label>
                                    <select name="store_website_id" id="" class="form-control" required>
                                        <option value="">Select</option>
                                        @foreach($store_websites as $website)
                                        <option value="{{$website->id}}">{{$website->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Api Key</label>
                                    <input type="text" class="form-control" name="api_key" required/>
                                </div>
                                <div class="col-md-4">
                                    <label>Instance Url</label>
                                    <input type="text" class="form-control" name="url" required/>
                                </div>
                                
                            </div>
                        </div>
                        <br>
                        <div class="modal-footer mt-5">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-secondary save-account">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!--Update Account Modal -->
        <div class="modal fade" id="updateAccount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update Watson Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="" id="edit-watson-account">
                        @csrf
                        <input type="hidden" id="account_id">
                        <div class="modal-body mb-2">
                        <div class="col-md-12">
                                <div class="col-md-6 form-group">
                                    <label>Website</label>
                                    <select name="store_website_id" id="store_website_id" class="form-control" required>
                                        
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Api Key</label>
                                    <input type="text" class="form-control" id="api_key" name="api_key" required/>
                                </div>
                            </div>
                            <div class="col-md-12">
                            <div class="col-md-10 form-group">
                                        <label>Instance Url</label>
                                        <input type="text" class="form-control" id="instance_url" name="url" required/>
                            </div>
                            <div class="col-md-2 form-group">
                                        <label>Is active</label>
                                        <input type="checkbox" class="form-control" id="is_active"  name="is_active"/>
                            </div>
                        </div>
                        </div>
                        
                        <br>
                        <div class="modal-footer mt-5">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-secondary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
<script>
$(document).on("submit","#submit-watson-account",function(e) {
            e.preventDefault();
            var postData = $(this).serialize();
            $.ajax({
                method : "post",
                url: "{{action('WatsonController@store')}}",
                data: postData,
                dataType: "json",
                success: function (response) {
                    if(response.code == 200) {
                        toastr["success"]("Status updated!", "Message")
                        $("#addAccount").modal("hide");
                        $("#submit-watson-account").trigger('reset');
                    }else{
                        toastr["error"](response.message, "Message");
                    }
                }, 
                error: function (error) {
                    toastr["error"](error.responseJSON.message, "Message");
                }
            });
});

$(document).on("click",".edit_account",function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            $.ajax({
                method : "GET",
                url: "/watson/account/"+id,
                dataType: "json",
                success: function (response) {
                    var option = '<option value="" >Select</option>';
                    $.each(response.store_websites, function(i, item) {
                            if(item['id'] == response.account.store_website_id) {
                                var selected = 'selected';
                            }
                            else {
                                var selected = ''; 
                            }
                            option = option + '<option value="'+item['id']+'" '+selected+' >'+item['title']+'</option>';
                        });
                        $('#store_website_id').html(option);
                        $('#api_key').val(response.account.api_key);
                        $('#instance_url').val(response.account.url);
                        $('#account_id').val(response.account.id);
                        if(response.account.is_active) {
                            $("#is_active").prop("checked", true);
                        }
                        else {
                            $("#is_active").prop("checked", false);
                        }
                        
                        $('#updateAccount').modal('show');
                }, 
                error: function (error) {
                    toastr["error"](error.responseJSON.message, "Message");
                }
            });
});

$(document).on("submit","#edit-watson-account",function(e) {
            e.preventDefault();
            var postData = $(this).serialize();
            var id = $('#account_id').val();
            $.ajax({
                method : "post",
                url: "/watson/account/"+id,
                data: postData,
                dataType: "json",
                success: function (response) {
                    if(response.code == 200) {
                        toastr["success"]("Status updated!", "Message")
                        $("#updateAccount").modal("hide");
                    }else{
                        toastr["error"](response.message, "Message");
                    }
                }, 
                error: function (error) {
                    toastr["error"](error.responseJSON.message, "Message");
                }
            });
});
</script>

@endsection
