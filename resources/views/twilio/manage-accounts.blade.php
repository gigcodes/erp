@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Manage Twilio Accounts

                    <div class="pull-right">
                        
                        <button type="button" class="btn btn-secondary mr-2 twilio_user_data" data-toggle="modal" data-target="#twilio_user_data" style="background: #fff !important;
                        border: 1px solid #ddd !important;
                        color: #757575 !important;">Twilio Agent</button>
                    </div>
            </h2>
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row ml-3 mr-3">
        <div class="col-md-12 margin-tb">
            <div class="row">
                <button class="btn btn-secondary" data-target="#addAccount" data-toggle="modal">+</button>
            </div>
            <div class="row mt-5">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th scope="col" class="text-center">#</th>
                            <th scope="col" class="text-center">Email ID</th>
                            <th scope="col" class="text-center">Account ID</th>
                            <th scope="col" class="text-center">Auth Token</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody class="text-center">
                        @if(isset($all_accounts))
                            @foreach($all_accounts as $accounts)
                                <tr>
                                    <td>#</td>
                                    <td>{{ $accounts->twilio_email }}</td>
                                    <td>{{ $accounts->account_id }}</td>
                                    <td>{{ $accounts->auth_token }}</td>
                                    <td>
                                        <a type="button" data-attr="{{ $accounts->id }}" data-email="{{ $accounts->twilio_email }}" data-account-id="{{ $accounts->account_id }}" data-auth-token="{{ $accounts->auth_token }}" class="btn btn-edit-template edit_account">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </a>
                                        <a type="button" href="{{ route('twilio-delete-account', $accounts->id) }}" data-id="1" class="btn btn-delete-template" onclick="return confirm('Are you sure you want to delete this account ?');">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                        <a href="{{ route('twilio-manage-numbers', $accounts->id) }}" type="button" class="btn btn-image">
                                            <img src="/images/forward.png" style="cursor: default;" width="2px;">
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!--Add Account Modal -->
        <div class="modal fade" id="addAccount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Twilio Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="{{ route('twilio-add-account') }}">
                        @csrf
                        <div class="modal-body mb-2">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <label>Email</label>
                                    <input type="text" class="form-control" name="email" required/>
                                </div>
                                <div class="col-md-4">
                                    <label>Account ID</label>
                                    <input type="text" class="form-control" name="account_id" required/>
                                </div>
                                <div class="col-md-4">
                                    <label>Auth Token</label>
                                    <input type="text" class="form-control" name="auth_token" required/>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer mt-5">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
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
                        <h5 class="modal-title" id="exampleModalLabel">Update Twilio Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="{{ route('twilio-add-account') }}">
                        @csrf
                        <div class="modal-body mb-2">
                            <div class="col-md-12">
                                <input type="hidden" name="id" id="id" />
                                <div class="col-md-4">
                                    <label>Email</label>
                                    <input type="text" class="form-control" name="email" id="email" required/>
                                </div>
                                <div class="col-md-4">
                                    <label>Account ID</label>
                                    <input type="text" class="form-control" name="account_id" id="account_id" required/>
                                </div>
                                <div class="col-md-4">
                                    <label>Auth Token</label>
                                    <input type="text" class="form-control" name="auth_token" id="auth_token" required/>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer mt-5">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>


    <div class="modal fade" id="twilio_user_data" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >Twilio User List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="twlio_user_form" class="twlio_user_form">
            @csrf
            <div class="modal-body">
                <div class="col-xs-12 col-sm-12 col-md-12">
                <strong>User:</strong>
                        <input type="text" id="myInputTwilioUser" placeholder="Search User .." class="form-control search-role mb-3">
                        <div class="overflow-auto" id="collapse1" style="height:400px;overflow-y:scroll;">
                        
                        <ul id="myRole" class="padding-left-zero">
                        @foreach($twilio_user_list as $key => $user)
                           <li style="list-style-type: none;">
                           <!-- (in_array($value, $userRole)) ? "checked" : '') -->
                            <a>
                            <input type="checkbox" name="user_rec[]" value="{{$user->id}}" {{ $user->status == 1 ? 'checked' : '' }} >
                            <strong>{{$user->name}}</strong></a>
                            </li>
                       @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                <button type="button" class="btn btn-secondary add_twilio_user">Save changes</button>
            </div>
            </form>
            </div>
        </div>
    </div>

<script type="text/javascript">
    $(document).ready(function(){
       $('.edit_account').on("click", function(){
            $('#id').val($(this).data('attr'));
            $('#email').val($(this).data('email'));
            $('#account_id').val($(this).data('account-id'));
            $('#auth_token').val($(this).data('auth-token'));
            $('#updateAccount').modal('show');
       }) ;
    });


    $('#myInputTwilioUser').on("keyup", function(){
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById("myInputTwilioUser");
        filter = input.value.toUpperCase();
        ul = document.getElementById("myRole");
        li = ul.getElementsByTagName("li");
        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByTagName("a")[0];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    });

    $('.add_twilio_user').on("click", function(e){
        // var form_data = $('.twlio_user_form').serialize();
        var val = [];
        $(':checkbox:checked').each(function(i){
          val[i] = $(this).val();
        });

        $.ajax({
            type: "POST",
            url: "{{ route('twilio.add_user') }}",  
            data: {
                form_data:val
            },
            beforeSend : function() {
                
            },
            success: function (response) {
                if(response.status == 1){
                    toastr['success'](response.message);
                    setTimeout(function(){ location.reload(); }, 2000);
                }
            },
            error: function (response) { 
               
            }
        });
    });
</script>
@endsection
