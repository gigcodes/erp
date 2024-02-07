    @if(auth()->user())
    <!-- sop-search Modal-->
    <div id="menu-sop-search-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Sop Search</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex" id="search-bar">
                                <input type="text" value="" name="search" id="menu_sop_search" class="form-control" placeholder="Search Here.." style="width: 30%;">
                                <a title="Sop Search" type="button" class="sop_search_menu btn btn-sm btn-image " style="padding: 10px"><span>
                                    <img src="{{asset('images/search.png')}}" alt="Search"></span></a>
                                <button type="button" class="btn btn-secondary1 mr-2 addnotesop" data-toggle="modal" data-target="#exampleModalAppLayout">Add Notes</button>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered page-notes" style="font-size:13.8px;border:0px !important; table-layout:fixed" id="NameTable-app-layout">
                                    <thead>
                                    <tr>
                                        <th width="2%">ID</th>
                                        <th width="10%">Name</th>
                                        <th width="14%">Content</th>
                                        <th width="5%">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody class="sop_search_result">
                                   
                                    @foreach ($usersop as $key => $value)
                                        <tr id="sid{{ $value->id }}" class="parent_tr" data-id="{{ $value->id }}">
                                            <td class="sop_table_id">{{ $value->id }}</td>
                                            <td class="expand-row-msg" data-name="name" data-id="{{$value->id}}">
                                                <span class="show-short-name-{{$value->id}}">{{ Str::limit($value->name, 17, '..')}}</span>
                                                <span style="word-break:break-all;" class="show-full-name-{{$value->id}} hidden">{{$value->name}}</span>
                                            </td>
                                            <td class="expand-row-msg Website-task " data-name="content" data-id="{{$value->id}}">
                                                <span class="show-short-content-{{$value->id}}">{{ Str::limit($value->content, 50, '..')}}</span>
                                                <span style="word-break:break-all;" class="show-full-content-{{$value->id}} hidden">{{$value->content}}</span>
                                            </td>
                                            <td class="p-1">
                                                <a href="javascript:;" data-id="{{ $value->id }}" class="menu_editor_edit btn btn-xs p-2" >
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="javascript:;" data-id="{{ $value->id }}" data-content="{{$value->content}}" class="menu_editor_copy btn btn-xs p-2" >
                                                    <i class="fa fa-copy"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- user-search Modal-->
    <div id="menu-user-search-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">User Search</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex" id="search-bar">
                                <input type="text" value="" name="search" id="menu_user_search" class="form-control" placeholder="Search Here.." style="width: 30%;">
                                <a title="User Search" type="button" id="menu-user-search-btn" class="menu-user-search-btn btn btn-sm btn-image " style="padding: 10px"><span>
                                    <img src="{{asset('images/search.png')}}" alt="Search"></span></a>
                                <span class="processing-txt d-none">{{ __('Loading...') }}</span>    
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="table table-bordered table-responsive mt-3">
                                <table class="table table-bordered page-notes" style="font-size:13.8px;border:0px !important; table-layout:fixed" id="NameTable-app-layout">
                                    <thead>
                                    <tr>
                                        <th width="10%">ID</th>
                                        <th width="30%">Name</th>
                                        <th width="30%">Email</th>
                                        <th width="30%">Phone</th>
                                    </tr>
                                    </thead>
                                    <tbody class="user_search_global_result">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    

    <!-- sop-add Modal-->
    <div class="modal fade" id="exampleModalAppLayout" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="FormModalAppLayout">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name-app-layout" name="name" required />
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select name="category[]" id="categorySelect-app-layout" class="globalSelect2 form-control" data-ajax="{{route('select2.sop-categories')}}" data-minimuminputlength="1" multiple></select>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="content">Content</label>
                            <input type="text" class="form-control" id="content-app-layout" required />
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btnsave" id="btnsave">Submit</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- email-search Modal-->
    <div id="menu-email-search-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl"  role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Email Search</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex" id="search-bar">
                                <input type="text" value="" name="search" id="menu_email_search" class="form-control" placeholder="Search Here.." style="width: 30%;">
                                <a title="Email Search" type="button" class="email_search_menu btn btn-sm btn-image " style="padding: 10px"><span>
                                    <img src="{{asset('images/search.png')}}" alt="Search"></span></a>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered page-notes" style="font-size:13.8px;border:0px !important;" id="emailNameTable">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Sender</th>
                                        <th>Receiver</th>
                                        <th>Subject & Body</th>
                                        <th>Action</th>
                                        <th>Read</th>
                                    </tr>
                                    </thead>
                                    <tbody class="email_search_result">
                                        
                                        @foreach ($userEmails as $key => $userEmail)
                                            <tr>
                                                <td>{{ Carbon\Carbon::parse($userEmail->created_at)->format('d-m-Y H:i:s') }}</td>
                                                <td class="expand-row-email" style="word-break: break-all">
                                                    <span class="td-mini-email-container">
                                                       {{ strlen($userEmail->from) > 30 ? substr($userEmail->from, 0, 15).'...' :  $userEmail->from }}
                                                    </span>
                                                    <span class="td-full-email-container hidden">
                                                        {{ $userEmail->from }}
                                                    </span>
                                                </td>
                                                <td class="expand-row-email" style="word-break: break-all">
                                                    <span class="td-mini-email-container">
                                                       {{ strlen($userEmail->to) > 30 ? substr($userEmail->to, 0,15).'...' :  $userEmail->to }}
                                                    </span>
                                                    <span class="td-full-email-container hidden">
                                                        {{ $userEmail->to }}
                                                    </span>
                                                </td>
                                                <td data-toggle="modal" data-target="#view-quick-email" onclick="openQuickMsg({{json_encode($userEmail)}})" style="cursor: pointer;">{{ substr($userEmail->subject, 0,  15) }} {{strlen($userEmail->subject) > 10 ? '...' : '' }}</td>
                                                <td>
                                                    <a href="javascript:;" data-id="{{ $userEmail->id }}" data-content="{{$userEmail->message}}" class="menu_editor_copy btn btn-xs p-2" >
                                                        <i class="fa fa-copy"></i>
                                                </a></td>
                                                <td>
                                                    <input type="checkbox" name="email_read" id="is_email_read" value="1" data-id="{{ $userEmail->id }}" onclick="updateReadEmail(this)">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="view-quick-email" class="modal" tabindex="-1" role="dialog" style="z-index: 99999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">View Email</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('emails.shortcuts')
                    <p><strong>Subject : </strong><input type="text" id="quickemailSubject" name="subject" class="form-control"></p>
                    <p><strong>Body : </strong><textarea id="reply-message" name="message" class="form-control reply-email-message"></textarea></p>
                    </br>
                    <p>
                        <strong>Message Body : </strong> - <span id="quickemailDate"></span>
                        <span class="pull-right"><label>History : <input type="checkbox" name="pass_history" id="pass_history" value="1" style=" height: 13px;"></label></span>
                    </p>
                    <input type="hidden" id="receiver_email">
                    <input type="hidden" id="sender_email_address">
                    <input type="hidden" id="reply_email_id">
                    <div id="formattedContent"></div>

                        <div class="col-md-12">
                            <iframe src="" id="eFrame" scrolling="no" style="width:100%;" frameborder="0" onload="autoIframe('eFrame');"></iframe>
                        </div>
                        <div class="modal-footer" style=" width: 100%; display: inline-block;">
                            <label style=" float: left;"><span>Unread :</span> <input type="checkbox" id="unreadEmail" value="" style=" height: 13px;"></label>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-default submit-reply-email">Reply</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <div id="menu-sopupdate" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Data</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo route('updateName'); ?>" id="menu_sop_edit_form">
                        <input type="text" hidden name="id" id="sop_edit_id">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="hidden" class="form-control sop_old_name" name="sop_old_name" id="sop_old_name"
                                   value="">
                            <input type="text" class="form-control sopname" name="name" id="sop_edit_name">
                        </div>
                        <div class="form-group">
                            <label for="name">Category</label>
                            <input type="hidden" class="form-control sop_old_category" name="sop_old_category" id="sop_old_category"
                                   value="">
                            <input type="text" class="form-control sopcategory" name="category" id="sop_edit_category">
                        </div>

                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea class="form-control sop_edit_class" name="content" id="sop_edit_content"></textarea>
                        </div>

                        <button type="submit" class="btn btn-secondary ml-3 updatesopnotes">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="declien-remarks" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Remarks</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo route('appointment-request.declien.remarks'); ?>" id="menu_sop_edit_form">
                        <input type="text" hidden name="appointment_requests_id" id="appointment_requests_id">
                        @csrf
                        <div class="form-group">
                            <label for="content">Remarks</label>
                            <textarea class="form-control sop_edit_class" name="appointment_requests_remarks" id="appointment_requests_remarks"></textarea>
                            <span class="text-danger"></span>
                        </div>

                        <button type="button" class="btn btn-secondary ml-3 updatedeclienremarks">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @auth
        @if(auth()->user()->isAdmin())
            <div id="quickRequestZoomModal" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <form action="#" method="POST" id="send-request-form">
                            @csrf

                            <div class="modal-header">
                                <h4 class="modal-title">Send Request</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="title">Select User</label>
                                    <select name="requested_ap_user_id" class="form-control" style="width: 100%" id="requested_ap_user_id" required>
                                        <option>--Users--</option>
                                        @foreach ($users as $key => $user)
                                            @if($user->id!=auth()->user()->id)
                                                @if($user->isOnline()==1)
                                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    </select>
                                </div> 

                                <div class="form-group">
                                    <label>Remarks:</label>
                                    <textarea name="requested_ap_remarks" id="requested_ap_remarks" placeholder="Enter remarks" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-secondary send-ap-quick-request">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    <!-- sop-search Modal-->
    <div id="commandResponseHistoryModelHeader" class="modal fade" role="dialog" style="z-index:2000">
    <div class="modal-dialog modal-lg" style="max-width: 100%;width: 90% !important;">
        <!-- Modal content-->
        <div class="modal-content ">
            <div id="add-mail-content">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Command Response History</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 3%;">ID</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">User Name</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">Command Name</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">Status</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">Response</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">Request</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">Job ID</th>
                                    <th style="width: 4%;overflow-wrap: anywhere;">Date</th>
                                </tr>
                            </thead>
                            <tbody class="tbodayCommandResponseHistory">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <div id="addPostman_header" tabindex="-2"  class="modal fade" role="dialog" style="z-index: 5000; ">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content ">
            <div id="add-mail-content">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><span id="titleUpdate">Add</span> Command</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="magentoForm" method="post">
                            @csrf

                            <div class="form-row">
                                <input type="hidden" id="command_id" name="id" value="" />

                                @auth
                                    @if(auth()->user()->isAdmin())
                                        <div class="form-group col-md-12">
                                            <label for="title">User Name</label>
                                            <select name="user_permission[]" multiple class="form-control dropdown-mul-1" style="width: 100%" id="user_permission" required>
                                                <option>--Users--</option>
                                                @foreach ($users as $key => $user)
                                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                    @endif
                                @endauth

                                <div class="form-group col-md-12">
                                    <label for="title">Website</label>
                                    <div class="dropdown-sin-1">
                                        
                                        <select name="websites_ids[]" class="websites_ids form-control dropdown-mul-1" style="width: 100%;" id="websites_ids" required>
                                            <option>--Website--</option>
                                            <option value="ERP">ERP</option>
                                            <?php
                            foreach($websites as $website){
                                echo '<option value="'.$website->id.'" data-website="'.$website->website.'">'.$website->title.'</option>';
                            }
                          ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="assets_manager_id">Assets Manager <span id="am-client-id"></span></label>
                                    <div class="dropdown-sin-1">

                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="command_name">Command Name</label>
                                    {{-- <input type="text" name="command_name" value="" class="form-control" id="command_name_search" placeholder="Enter Command name"> --}}

                                </div>
                                <div class="form-group col-md-12">
                                    <label for="command_type">Command</label>
                                    {{-- <input type="text" name="command_type" value="" class="form-control" id="command_type" placeholder="Enter request type"> --}}

                                </div>
                                <div class="form-group col-md-12">
                                    <label for="working_directory">Working Directory</label>
                                    <input type="text" name="working_directory" value="" class="form-control" id="working_directory" placeholder="Enter the working directory" required>
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-secondary submit-form">Save</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="instructionAlertModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Instruction Reminder</h3>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <a href="" id="instructionAlertUrl" class="btn btn-secondary mx-auto">OK</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="developerAlertModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Developer Task Reminder</h3>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <a href="" id="developerAlertUrl" class="btn btn-secondary mx-auto">OK</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="masterControlAlertModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Master Control Alert</h3>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <a href="" id="masterControlAlertUrl" class="btn btn-secondary mx-auto">OK</a>
                </div>
            </div>
        </div>
    </div>

    <div id="permission-request-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Permission request list</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <button type="button" class="btn btn-default permission-delete-grant">Delete
                        All</button>
                    <div class="col-md-12" id="permission-request">
                        <table class="table fixed_header">
                            <thead>
                                <tr>
                                    <th>User name</th>
                                    <th>Permission name</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="show-list-records">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="showLatestEstimateTime" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl"  style="width: 100% !important; max-width: 1700px !important;">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Estimation</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body shortcut-estimate-search-container">
                    <div class="from-group ">
                        <label>Search</label>
                        <br>
                        <select name="task_id" id="shortcut-estimate-search" class="form-control">
                            <option selected value>Select task</option>
                            @foreach ($d_taskList as $val)
                                <option value="DEVTASK-{{$val}}">DEVTASK-{{$val}}</option>
                            @endforeach
                            @foreach ($g_taskList as $val)
                                <option value="TASK-{{$val}}">TASK-{{$val}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-table">

                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="todolist-request-model" class="modal fade" role="dialog">
        <div class="modal-content modal-dialog modal-md">
            <form action="{{ route('todolist.store') }}" method="POST" onsubmit="return false;">
                @csrf

                <div class="modal-header">
                    <h4 class="modal-title">Create Todo List</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body show-list-records" id="todolist-request">
                    <div class="form-group">
                        <strong>Title:</strong>
                        <input type="text" name="title" class="form-control add_todo_title"
                            value="{{ old('title') }}" required="">
                        <span class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <strong>Subject:</strong>
                        <input type="text" name="subject" class="form-control add_todo_subject"
                            value="{{ old('subject') }}" required="">
                        <span class="text-danger"></span>
                    </div>
                    
                     <div class="form-group">
                         <strong>Category:</strong>
                         {{-- <input type="text" name="" class="form-control" value="{{ old('') }}" required> --}}
                         <select name="todo_category_id" class="form-control add_todo_category">
                            <option value="">Select Category</option>
                            <option value="-1">Add New Category</option>
                            @foreach($todoCategories as $todoCategory)
                                <option value="{{$todoCategory->id}}" @if($todoCategory->id == old('todo_category_id')) selected @endif>{{$todoCategory->name}}</option>
                            @endforeach
                         </select>
                         <span class="text-danger"></span>
                     </div>
                    
                    <div class="form-group othercat" style="display: none;">
                        <strong>Add New Category:</strong>
                        <input type="text" name="other" class="form-control add_todo_other" value="{{ old('other') }}">
                        <span class="text-danger"></span>
                    </div>

                    
                    <div class="form-group">
                        <strong>Status:</strong>
                        {{-- <input type="text" name="status" class="form-control" value="{{ old('status') }}" required> --}}
                        <select name="status" class="form-control add_todo_status">
                            @foreach ($statuses as $status )
                            <option value="{{$status['id']}}" @if (old('status') == $status['id']) selected @endif>{{$status['name']}}</option>
                            @endforeach
                        </select>
                        <span class="text-danger"></span>
                    </div>
                    <div class="form-group" style="margin-bottom: 0px;">
                        <strong>Date:</strong>

                        <div class='input-group date' id='todo-date' required="">
                            <input type="text" class="form-control global add_todo_date" name="todo_date" placeholder="Date"
                                value="{{ old('todo_date') }}">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <span class="text-danger text-danger-date"></span>

                    <div class="form-group" style="margin-top: 15px;">
                        <strong>Remark:</strong>
                        <input type="text" name="remark" class="form-control add_todo_remark" value="{{ old('remark') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary submit-todolist-button">Store</button>
                </div>
            </form>
        </div>
    </div>

    <div id="menu-create-database-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" role="document" style="width:500px !important">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Database</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form id="database-form">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="database_user_id" class="app-database-user-id" id="database-user-id" value="">
                                    <div class="row">
                                        <div class="col">
                                            <select class="form-control choose-db" name="connection">
                                                <?php foreach (\App\StoreWebsite::DB_CONNECTION as $k => $connection) {?>
                                                <option {{($connection == $k)?"selected='selected'":''}} value="<?php echo $k; ?>"><?php echo $connection; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select class="form-control choose-username" name="username">
                                                <option value="">Select User</option>
                                                <?php
                                                
                                                foreach ($users as $k => $connection) {?>
                                                <option value="<?php echo $connection->id; ?>" data-name="{{$connection->name}}"><?php echo $connection->name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <input type="text" name="password" class="database_password" class="form-control" placeholder="Enter password">
                                        </div>
                                        <div class="col">
                                            <button type="button" class="btn btn-secondary btn-database-add" data-id="">ADD</button>

                                            <button type="button" class="btn btn-secondary btn-delete-database-access d-none" data-connection="" data-id="">DELETE ACCESS</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <form>
                                <?php echo csrf_field(); ?>
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col">
                                            <input type="hidden" name="connection"  value="">
                                            <input type="text" name="search" class="form-control app-search-table" placeholder="Search Table name">
                                        </div>
                                        <div class="col">
                                            <div class="form-group col-md-5">
                                                <select class="form-control assign-permission-type" name="assign_permission">
                                                    <option value="read">Read</option>
                                                    <option value="write">Write</option>
                                                </select>
                                            </div>
                                            <button type="button" class="btn btn-secondary btn-assign-permission assign-permission" data-id="">Assign Permission</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-2">
                                    <table class="table table-bordered" id="database-table-list1">
                                        <thead>
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="95%">Table name</th>
                                        </tr>
                                        </thead>
                                        <tbody class="menu_tbody">
                                            @php
                                              $database_table_name = \DB::table('information_schema.TABLES')->where('table_schema', env('DB_DATABASE'))->get();
                                            @endphp
                                            @foreach(json_decode($database_table_name) as $name)
                                            <tr>
                                                <td><input type="checkbox" name="tables[]" value="{{$name->TABLE_NAME}}"></td>
                                                <td>{{$name->TABLE_NAME}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <div id="menu-show-task-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Task & Activity</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="database-form">
                                <?php echo csrf_field(); ?>
                                <div class="row">
                                    <div class="col-12 pb-3">
                                        <input type="text" name="task_search" class="task-search-table" class="form-control" placeholder="Enter Task Id & Keyword">
                                        
                                        <select class="form-control col-md-2 ml-3 ipusersSelect" name="task_user_id" id="task_user_id">
                                            <option value="">Select user</option>
                                                @foreach ($userLists as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            <option value="other">Other</option>
                                        </select>
                                        <button type="button" class="btn btn-secondary btn-task-search-menu" ><i class="fa fa-search"></i></button>
                                    </div>
                                    <div class="col-12">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                            <tr>
                                                <th width="5%">ID</th>
                                                <th width="10%">Assign To</th>
                                                <th width="10%">Communication</th>
                                            </tr>
                                            </thead>
                                            <tbody class="show-search-task-list">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="menu-show-dev-task-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Dev Task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="database-form">
                                <?php echo csrf_field(); ?>
                                <div class="row">
                                    <div class="col-12 pb-3">
                                        <input type="text" name="task_search" class="dev-task-search-table" class="form-control" placeholder="Enter Dev Task Id & Keyword">
                                        
                                        <select class="form-control col-md-2 ml-3 ipusersSelect" name="quicktask_user_id" id="quicktask_user_id">
                                            <option value="">Select user</option>
                                                @foreach ($userLists as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            <option value="other">Other</option>
                                        </select>
                                        <button type="button" class="btn btn-secondary btn-dev-task-search-menu" ><i class="fa fa-search"></i></button>
                                    </div>
                                    <div class="col-12">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                            <tr>
                                                <th width="5%">ID</th>
                                                <th width="10%">Assign To</th>
                                                <th width="10%">Communication</th>
                                            </tr>
                                            </thead>
                                            <tbody class="show-search-dev-task-list">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="menu-todolist-get-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Todo List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="database-form">
                                <?php echo csrf_field(); ?>
                                <div class="row">
                                    <div class="col-12 pb-3">
                                        <div class="row">
                                            <div class="col-4 pr-0">
                                                <label for="todolist_search">Search Keyword:</label>
                                                <input type="text" name="todolist_search" class="dev-todolist-table" class="form-control" placeholder="Search Keyword" style=" width: 100%;">
                                            </div>
                                            <div class="col-3 pr-0">
                                                <div class="form-group">
                                                    <label for="start_date">Start Date:</label>
                                                    <input type="date" class="form-control" id="todolist_start_date" name="start_date">
                                                </div>
                                            </div>
                                            <div class="col-3 pr-0">
                                                <div class="form-group">
                                                    <label for="end_date">End Date:</label>
                                                    <input type="date" class="form-control" id="todolist_end_date" name="end_date">
                                                </div>
                                            </div>
                                            <div class="col-2 pr-0">
                                                <div class="form-group">
                                                    <label for="button" style=" width: 100%;">&nbsp;</label>
                                                    <button type="button" class="btn btn-secondary btn-todolist-search-menu" ><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Subject</th>
                                                <th>Category</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                            </thead>
                                            <tbody class="show-search-todolist-list">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="menu_user_history_modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User history</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12" id="user_history_div">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User type</th>
                                    <th>Previous user</th>
                                    <th>New User</th>
                                    <th>Updated by</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="menu_confirmMessageModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirm Message</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{ route('task_category.store') }}" method="POST" onsubmit="return false;">
                    @csrf

                    <div class="modal-body">


                        <div class="form-group">
                            <div id="message_confirm_text"></div>
                            <input name="task_id" id="confirm_task_id" type="hidden" />
                            <input name="message" id="confirm_message" type="hidden" />
                            <input name="status" id="confirm_status" type="hidden" />
                        </div>
                        <div class="form-group">
                            <p>Send to Following</p>
                            <input checked="checked" name="send_message_recepients[]" class="send_message_recepients" type="checkbox" value="assign_by">Assign By
                            <input checked="checked" name="send_message_recepients[]" class="send_message_recepients" type="checkbox" value="assigned_to">Assign To
                            <input checked="checked" name="send_message_recepients[]" class="send_message_recepients" type="checkbox" value="master_user_id">Lead 1
                            <input checked="checked" name="send_message_recepients[]" class="send_message_recepients" type="checkbox" value="second_master_user_id">Lead 2
                            <input checked="checked" name="send_message_recepients[]" class="send_message_recepients" type="checkbox" value="contacts">Contacts
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary menu-confirm-messge-button">Send</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div id="menu-upload-document-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Document</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="menu-upload-task-documents">
                    <div class="modal-body">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" id="hidden-identifier" name="developer_task_id" value="">
                        <div class="row">
                            <div class="col-md-10 col-md-offset-1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Subject</label>
                                            <?php echo Form::text("subject",null, ["class" => "form-control", "placeholder" => "Enter subject"]); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <?php echo Form::textarea("description",null, ["class" => "form-control", "placeholder" => "Enter Description"]); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Documents</label>
                                            <input type="file" name="files[]" id="filecount" multiple="multiple">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="menu-blank-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="menu-file-upload-area-section" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('task.save-documents') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="task_id" id="hidden-task-id" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload File(s)</h4>
                    </div>
                    <div class="modal-body" style="background-color: #999999;">
                        @csrf
                        <div class="form-group">
                            <label for="document">Documents</label>
                            <input type="file" name="document" class="needsclick" id="document-dropzone" multiple>
{{--                                <div class="needsclick dropzone" id="document-dropzone">--}}

{{--                                </div>--}}
                        </div>
                        <div class="form-group add-task-list">

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default menu-btn-save-documents">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="menu-preview-task-image" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="width:1%;">No</th>
                                <th style=" width: 30%">Files</th>
                                <th style="word-break: break-all; width:12%">Send to</th>
                                <th style="width: 1%;">User</th>
                                <th style="width: 11%">Created at</th>
                                <th style="width: 6%">Action</th>
                            </tr>
                            </thead>
                            <tbody class="menu-task-image-list-view">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="create-manual-payment" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content" id="create-manual-payment-content">

            </div>
        </div>
    </div>

    @if(Auth::check())

    <div id="Create-Sop-Shortcut" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Shortcut Model</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="createShortcutForm">
                    <td><input type="file" name="image" hidden></td>
                    <td><input type="text" name="tags[0][name]" hidden></td>
                    <td><input type="text" name="tags[0][value]" hidden></td>
                    <div class="modal-body add_sop_modal">
                        <div class="mb-3">
                            <select class="form-control sop_drop_down ">
                                <option value="sop">Sop</option>
                                <option value="knowledge_base">Knowledge Base</option>
                                <option value="code_shortcut">Code Shortcut</option>
                            </select>
                        </div>
                        <input type="hidden" name="chat_message_id" value="" class="chat_message_id" />
                        <div class="add_sop_div mt-3">
                            <div>
                                <select class="form-control knowledge_base mb-3" name="sop_knowledge_base" hidden>
                                    <option value="">Select</option>
                                    <option value="book">Book</option>
                                    <option value="chapter">Chapter</option>
                                    <option value="page">Page</option>
                                    <option value="shelf">Shelf</option>
                                </select>
                            </div>
                            <div>
                                <span class="books_error" style="color:red;"></span>
                            </div>
                            <div>
                                <td>Name:</td>
                                <td><input type="text" name="name" class="form-control mb-3 name" placeholder="Enter Name"></td>
                            </div>
                            <div>
                                <td>Category:</td>
                                <td><input type="text" name="category" class="form-control mb-3 category" placeholder="Enter Category" value="Sop"></td>
                            </div>
                            <div>
                                <td>Description:</td>
                                <td><textarea name="description" id="" cols="30" rows="10" class="form-control sop_description" placeholder="Enter Description"></textarea></td>
                            </div>
                            <div class="sop_solution hidden">
                                <td>Solution:</td>
                                <td><textarea name="solution" id="" cols="30" rows="10" class="form-control" placeholder="Enter Solution"></textarea></td>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default create_shortcut_submit">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    @endif


    <div id="system-request" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="width: 1000px; max-width: 1000px;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">System IPs</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12" id="permission-request">
                        @php
                        
                       

                        $shell_list = shell_exec('bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . '/webaccess-firewall.sh
                        -f list');
                        $final_array = [];
                        if ($shell_list != '') {
                        $lines = explode(PHP_EOL, $shell_list);
                        $final_array = [];
                        foreach ($lines as $line) {
                        $values = [];
                        $values = explode(' ', $line);
                        array_push($final_array, $values);
                        }
                        }
                        @endphp

                        <div id="select-user">
                            <input type="text" name="add-ip" class="form-control col-md-3" placeholder="Add IP here...">
                            <select class="form-control col-md-2 ml-3 ipusersSelect" name="user_id" id="ipusers">
                                <option value="">Select user</option>
                                @foreach ($userLists as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                                <option value="other">Other</option>
                            </select>
                            <input type="text" name="other_user_name" id="other_user_name"
                                class="form-control col-md-2 ml-3" style="display:none;" placeholder="other name">
                            <input type="text" name="ip_comment" class="form-control col-md-2 ml-3 mr-3""
                            placeholder="Add comment...">
                            <button class="btn-success btn addIp ml-3 mb-5">Add</button>
                            <button class="btn-warning btn bulkDeleteIp ml-3 mb-5">Delete All IPs</button>
                        </div>



                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Index</th>
                                <th>IP</th>
                                <th>User</th>
                                <th>Source</th>
                                <th>Comment</th>
                                <th>Command</th>
                                <th>Status</th>
                                <th>Message</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="userAllIps">
                            </tbody>
                            
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @endif
