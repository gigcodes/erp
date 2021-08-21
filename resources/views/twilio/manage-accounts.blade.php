@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Manage Twilio Accounts

                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary mr-2 twilio_key_option" data-toggle="modal" data-target="#twilio_key_option_modal" style="background: #fff !important;
                        border: 1px solid #ddd !important;
                        color: #757575 !important;">Twilio Key Options</button>
                    </div>

                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary mr-2 twilio_working_hours" data-toggle="modal" data-target="#twilio_working_hours" style="background: #fff !important;
                        border: 1px solid #ddd !important;
                        color: #757575 !important;">Set Working Hours</button>
                    </div>

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


    <div class="modal fade" id="twilio_working_hours" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >Twilio Working Hours</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="twlio_user_form" class="twlio_user_form">
            @csrf
            <div class="modal-body">
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" style="table-layout:fixed;">
                        <thead>
                        <tr>
                            <th scope="col" width="40%">Website</th>
                            <th scope="col" width="20%">Start Time</th>
                            <th scope="col" width="20%">End Time</th>
                            <th scope="col" width="10%"></th>
                        </tr>
                        </thead>
                        <tbody class="">
                            @foreach($store_websites as $key => $value)
                            <tr>
                                <td style="word-break: break-all;">{{$value->website}}</td>
                                <td><input type="time" name="start_time" class="start_time_{{$value->id}}" style="width: -webkit-fill-available;" value="{{$value->start_time}}" /> </td>
                                <td><input type="time" name="end_time" class="end_time_{{$value->id}}" style="width: -webkit-fill-available;" value="{{$value->end_time}}" /></td>
                                <td><button type="button" data-id="{{$value->id}}" class="btn btn-secondary set_twilio_storewebsite_time">Save</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                
            </div>
            </form>
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
                    <strong>Store Website:</strong>
                    <select class="form-control store_website_user mb-5" name="store_website_user">
                        <option value="">Select</option>
                        @foreach($store_websites as $key => $value)
                            <option value="{{$value->id}}" >{{$value->website}}</option>
                        @endforeach
                    </select>

                    <div class="user_list_data d-none">
                        <strong>User:</strong>
                        <input type="text" id="myInputTwilioUser" placeholder="Search User .." class="form-control search-role mb-3">

                        <div class="overflow-auto" id="collapse1" style="height:400px;overflow-y:scroll;">
                        
                            <!-- <ul id="myRole" class="padding-left-zero">
                            @foreach($twilio_user_list as $key => $user)
                            <li style="list-style-type: none;">
                                <a>
                                <input type="checkbox" name="user_rec[]" value="{{$user->id}}" {{ $user->status == 1 ? 'checked' : '' }} >
                                <strong>{{$user->name}}</strong></a>
                                </li>
                            @endforeach
                            </ul> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer user_list_data d-none">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                <button type="button" class="btn btn-secondary add_twilio_user">Save changes</button>
            </div>
            </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="twilio_key_option_modal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >Twilio Key Options</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="twlio_key_option_form" class="twlio_key_option_form">
            @csrf
            <div class="modal-body">

                <strong>Store Website:</strong>
                <select class="form-control store_website_twilio_key mb-5" name="store_website_twilio_key">
                    <option value="">Select</option>
                    @foreach($store_websites as $key => $value)
                        <option value="{{$value->id}}" >{{$value->website}}</option>
                    @endforeach
                </select>

                <div class="table-responsive store_website_twilio_key_data d-none">
                    <table class="table table-bordered table-hover" style="table-layout:fixed;">
                        <thead>
                        <tr>
                            <th scope="col" width="20%" class="text-center">Key</th>
                            <th scope="col" width="20%">Option</th>
                            <th scope="col" width="50%">Description</th>
                            <th scope="col" width="10%"></th>
                        </tr>
                        </thead>
                        <tbody class="">
                            <tr>
                                <td><h3 class="text-center">1</h3></td>
                                <td>
                                    <select class="form-control mb-2 option_menu_1" aria-label="Default select example">
                                    <option value="">Select</option>
                                    <option value="order" {{ isset($twilio_key_arr[1]['option']) && $twilio_key_arr[1]['option'] == 'order' ? 'selected' : ''}}>Order</option>
                                    <option value="product" {{ isset($twilio_key_arr[1]['option']) && $twilio_key_arr[1]['option'] == 'product' ? 'selected' : ''}}>Product</option>
                                    </select>
                                </td>   
                                <td>
                                    <textarea class="form-control mb-2 key_description_1" rows="3">{{ isset($twilio_key_arr[1]['desc']) ? $twilio_key_arr[1]['desc'] : ''}}</textarea>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-secondary save_key_option" data-id="1">Save</a>
                                </td>
                            </tr>
                            <tr>
                                <td><h3 class="text-center">2</h3></td>
                                <td>
                                    <select class="form-control mb-2 option_menu_2" aria-label="Default select example">
                                        <option value="">Select</option>
                                        <option value="order" {{ isset($twilio_key_arr[2]['option']) && $twilio_key_arr[2]['option'] == 'order' ? 'selected' : ''}}>Order</option>
                                        <option value="product" {{ isset($twilio_key_arr[2]['option']) && $twilio_key_arr[2]['option'] == 'product' ? 'selected' : ''}}>Product</option>
                                    </select>
                                </td>   
                                <td>
                                    <textarea class="form-control mb-2 key_description_2" rows="3">{{ isset($twilio_key_arr[2]['desc']) ? $twilio_key_arr[2]['desc'] : ''}}</textarea>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-secondary save_key_option" data-id="2">Save</a>
                                </td>
                            </tr>
                            <tr>
                                <td><h3 class="text-center">3</h3></td>
                                <td>
                                    <select class="form-control mb-2 option_menu_3" aria-label="Default select example">
                                        <option value="">Select</option>
                                        <option value="order" {{ isset($twilio_key_arr[3]['option']) && $twilio_key_arr[3]['option'] == 'order' ? 'selected' : ''}}>Order</option>
                                        <option value="product" {{ isset($twilio_key_arr[3]['option']) && $twilio_key_arr[3]['option'] == 'product' ? 'selected' : ''}}>Product</option>
                                    </select>
                                </td>   
                                <td>
                                    <textarea class="form-control mb-2 key_description_3" rows="3">{{ isset($twilio_key_arr[3]['desc']) ? $twilio_key_arr[3]['desc'] : ''}}</textarea>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-secondary save_key_option" data-id="3">Save</a>
                                </td>
                            </tr>
                            <tr>
                                <td><h3 class="text-center">4</h3></td>
                                <td>
                                    <select class="form-control mb-2 option_menu_4" aria-label="Default select example">
                                        <option value="">Select</option>
                                        <option value="order" {{ isset($twilio_key_arr[4]['option']) && $twilio_key_arr[4]['option'] == 'order' ? 'selected' : ''}}>Order</option>
                                        <option value="product" {{ isset($twilio_key_arr[4]['option']) && $twilio_key_arr[4]['option'] == 'product' ? 'selected' : ''}}>Product</option>
                                    </select>
                                </td>   
                                <td>
                                    <textarea class="form-control mb-2 key_description_4" rows="3">{{ isset($twilio_key_arr[4]['desc']) ? $twilio_key_arr[4]['desc'] : ''}}</textarea>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-secondary save_key_option" data-id="4">Save</a>
                                </td>
                            </tr>
                            <tr>
                                <td><h3 class="text-center">5</h3></td>
                                <td>
                                    <select class="form-control mb-2 option_menu_5" aria-label="Default select example">
                                        <option value="">Select</option>
                                        <option value="order" {{ isset($twilio_key_arr[5]['option']) && $twilio_key_arr[5]['option'] == 'order' ? 'selected' : ''}}>Order</option>
                                        <option value="product" {{ isset($twilio_key_arr[5]['option']) && $twilio_key_arr[5]['option'] == 'product' ? 'selected' : ''}}>Product</option>
                                    </select>
                                </td>   
                                <td>
                                    <textarea class="form-control mb-2 key_description_5" rows="3">{{ isset($twilio_key_arr[5]['desc']) ? $twilio_key_arr[5]['desc'] : ''}}</textarea>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-secondary save_key_option" data-id="5">Save</a>
                                </td>
                            </tr>
                            <tr>
                                <td><h3 class="text-center">6</h3></td>
                                <td>
                                    <select class="form-control mb-2 option_menu_6" aria-label="Default select example">
                                        <option value="">Select</option>
                                        <option value="order" {{ isset($twilio_key_arr[6]['option']) && $twilio_key_arr[6]['option'] == 'order' ? 'selected' : ''}}>Order</option>
                                        <option value="product" {{ isset($twilio_key_arr[6]['option']) && $twilio_key_arr[6]['option'] == 'product' ? 'selected' : ''}}>Product</option>
                                    </select>
                                </td>   
                                <td>
                                    <textarea class="form-control mb-2 key_description_6" rows="3">{{ isset($twilio_key_arr[6]['desc']) ? $twilio_key_arr[6]['desc'] : ''}}</textarea>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-secondary save_key_option" data-id="6">Save</a>
                                </td>
                            </tr>
                            <tr>
                                <td><h3 class="text-center">7</h3></td>
                                <td>
                                    <select class="form-control mb-2 option_menu_7" aria-label="Default select example">
                                        <option value="">Select</option>
                                        <option value="order" {{ isset($twilio_key_arr[7]['option']) && $twilio_key_arr[7]['option'] == 'order' ? 'selected' : ''}}>Order</option>
                                        <option value="product" {{ isset($twilio_key_arr[7]['option']) && $twilio_key_arr[7]['option'] == 'product' ? 'selected' : ''}}>Product</option>
                                    </select>
                                </td>   
                                <td>
                                    <textarea class="form-control mb-2 key_description_7" rows="3">{{ isset($twilio_key_arr[7]['desc']) ? $twilio_key_arr[7]['desc'] : ''}}</textarea>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-secondary save_key_option" data-id="7">Save</a>
                                </td>
                            </tr>
                            <tr>
                                <td><h3 class="text-center">8</h3></td>
                                <td>
                                    <select class="form-control mb-2 option_menu_8" aria-label="Default select example">
                                        <option value="">Select</option>
                                        <option value="order" {{ isset($twilio_key_arr[8]['option']) && $twilio_key_arr[8]['option'] == 'order' ? 'selected' : ''}}>Order</option>
                                        <option value="product" {{ isset($twilio_key_arr[8]['option']) && $twilio_key_arr[8]['option'] == 'product' ? 'selected' : ''}}>Product</option>
                                    </select>
                                </td>   
                                <td>
                                    <textarea class="form-control mb-2 key_description_8" rows="3">{{ isset($twilio_key_arr[8]['desc']) ? $twilio_key_arr[8]['desc'] : ''}}</textarea>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-secondary save_key_option" data-id="8">Save</a>
                                </td>
                            </tr>
                            <tr>
                                <td><h3 class="text-center">9</h3></td>
                                <td>
                                    <select class="form-control mb-2 option_menu_9" aria-label="Default select example">
                                        <option value="">Select</option>
                                        <option value="order" {{ isset($twilio_key_arr[9]['option']) && $twilio_key_arr[9]['option'] == 'order' ? 'selected' : ''}}>Order</option>
                                        <option value="product" {{ isset($twilio_key_arr[9]['option']) && $twilio_key_arr[9]['option'] == 'product' ? 'selected' : ''}}>Product</option>
                                    </select>
                                </td>   
                                <td>
                                    <textarea class="form-control mb-2 key_description_9" rows="3">{{ isset($twilio_key_arr[9]['desc']) ? $twilio_key_arr[9]['desc'] : ''}}</textarea>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-secondary save_key_option" data-id="9">Save</a>
                                </td>
                            </tr>
                           

                        </tbody>
                    </table>
                </div>

            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary add_twilio_user">Save</button>
            </div> -->
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
        var website_id = $('.store_website_user').val();
        var val = [];
        $('.check_box:checkbox:checked').each(function(i){
          val[i] = $(this).val();
        });

        if(val.length == 0)
        {
            toastr['error']('Please Select Agent');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('twilio.add_user') }}",  
            data: {
                form_data:val,
                website_id:website_id
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

    
    $('.set_twilio_storewebsite_time').on("click", function(e){
       var id = $(this).data("id");

       var start_time = $('.start_time_'+id).val();
       var end_time = $('.end_time_'+id).val();
       if(start_time == ''){
            toastr['error']('Please Set Start Time');
            return false;
       }

       if(end_time == ''){
            toastr['error']('Please Set End Time');
            return false;
       }

       $.ajax({
            type: "POST",
            url: "{{ route('twilio.set_website_time') }}",  
            data: {
                _token: "{{csrf_token()}}",
                site_id:id,
                start_time:start_time,
                end_time:end_time
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

    $('#twilio_user_data').on('hidden.bs.modal', function () {
        location.reload();
    });


    
    $('.store_website_user').on("change", function(e){
        
        $(".user_list_data").removeClass("d-none");

        var website_id = $('.store_website_user').val();
        var user_html = '<ul id="myRole" class="padding-left-zero">';
        $.ajax({
            type: "GET",
            url: "{{ route('twilio.get_website_agent') }}",  
            data: {
                website_id:website_id,
            },
            beforeSend : function() {
                
            },
            success: function (response) {
                if(response.status == 1){
                    $.each( response.twilio_user_list, function( key, value ) {
                     
                        // if(value.store_website_id == null)
                        // {
                            user_html += ' <li style="list-style-type: none;">';
                            user_html += ' <a>';

                            if(value.status == 1 && value.website == website_id)
                                user_html += ' <input type="checkbox" class="check_box" name="user_rec[]" value="'+value.id+'" checked  >';
                            else if(value.is_same_website == 1)
                                user_html += ' <input type="checkbox" class="check_box" name="user_rec[]" value="'+value.id+'"  >';
                            else if(value.status == 1 && value.website != website_id)
                                user_html += ' <input type="checkbox" value="'+value.id+'" disabled checked  >';
                            else
                                user_html += ' <input type="checkbox" class="check_box" name="user_rec[]" value="'+value.id+'"  >';

                            user_html += '<strong>'+value.name+'</strong></a>';
                            user_html += ' </a>';
                            user_html += ' </li>'
                        // }
                    });
                }

                user_html += '</ul>'

                $('#collapse1').html(user_html);
            },
            error: function (response) { 
                
            }
        });
    });


    $('.save_key_option').on("click", function(e){
        var key_no = $(this).data("id");
        var option = $('.option_menu_'+key_no).val();
        var desc = $('.key_description_'+key_no).val();
        var website_id = $('.store_website_twilio_key').val();

        if(option == '')
        {
            toastr['error']('Please select Option');
            return false;
        }
        if(desc == '')
        {
            toastr['error']('Please Enter Description');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('twilio.set_twilio_key_options') }}",  
            data: {
                _token: "{{csrf_token()}}",
                key_no:key_no,
                option:option,
                description:desc,
                website_store_id:website_id,
            },
            beforeSend : function() {
                
            },
            success: function (response) {
                if(response.status == 1){
                    toastr['success'](response.message);
                }
            },
            error: function (response) { 
                
            }
        });
        
    });


    $('.store_website_twilio_key').on("change", function(e){
        
        $(".store_website_twilio_key_data").removeClass("d-none");
        var website_id = $('.store_website_twilio_key').val();

        $.ajax({
            type: "GET",
            url: "{{ route('twilio.get_website_wise_key_data') }}",  
            data: {
                _token: "{{csrf_token()}}",
                website_store_id:website_id,
            },
            beforeSend : function() {
                
            },
            success: function (response) {
                if(response.status == 1){
                    toastr['success'](response.message);
                }
            },
            error: function (response) { 
                
            }
        });
    });

    $('#twilio_key_option_modal').on('hidden.bs.modal', function () {
        $(".store_website_twilio_key_data").addClass("d-none");
    });
</script>
@endsection
