@extends('layouts.app')

@section('title', 'Post man Request')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<style>
  .multiselect {
    width: 100%;
  }

  .multiselect-container li a {
    line-height: 3;
  }
  .select2{
    width:200px !important;
  }
  #postmanform label{
    text-transform: capitalize;
    line-height: 31px;
  }
  .label-btn{
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .label-btn .btn{
    height: 30px !important;
    line-height: 17px;
    margin: 0 !important;
  }

  .modal-header .close {
    font-size: 23px;
    color: #000;
    opacity: 1;
    margin: 0rem 0rem 0rem auto;
  }
  .modal-content .modal-header .close{
    margin-top: -15px !important;

  }

  .modal-header .modal-title {
    font-size: 18px;
    font-weight: 600;
  }

  .custom-select + .select2.select2-container.select2-container--default{
    height: 32px;
  }
</style>

@endsection

@section('content')

<div class="row">
  <div class="col-12">
    <h2 class="page-heading">Postman Request ({{$counter}})</h2>
  </div>

  <div class="col-12 mb-3">
    <div class="pull-left">
    </div>
    <div class="pull-right">
      <!-- <a title="add new domain" class="btn btn-secondary add-new-btn">+</a> -->
    </div>
  </div>
</div>
<div class=" row " style="margin-left:20px;">
  <form class="form-inline" action="/postman/search" method="GET">
    <div class="col">
      <div class="form-group">
        <div class="input-group">
          <?php 
            //dd(request('folder_name'));
            if(request('folder_name')){   $folder_nameArr = request('folder_name'); }
            else{ $folder_nameArr = []; }
          ?>
          <select name="folder_name[]" class="form-control select2" multiple id="folder_name">
            <option value="" @if(count($folder_nameArr)==0) selected @endif>-- Select Folder --</option>
            <?php
            $ops = 'id';
            
            foreach ($folders as $folder) {
              $selected  = '';
              if (in_array($folder->id, $folder_nameArr))
                $selected  = 'selected';
              echo '<option value="' . $folder->id . '" ' . $selected . '>' . $folder->name . '</option>';
            }
            ?>
          </select>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="form-group">
        <div class="input-group">
          <?php 
            //dd(request('folder_name'));
            if(request('request_name')){   $request_nameArr = request('request_name'); }
            else{ $request_nameArr = []; }
          ?>
          <select name="request_name[]" class="form-control select2 custom-select" multiple id="request_name">
            <option value="" @if(count($request_nameArr)==0) selected @endif>-- Select Request Name --</option>
            @foreach ($listRequestNames as $key => $reqName)
            <?php
            $selected  = '';
            if (in_array($reqName, $request_nameArr)) {
              $selected  = 'selected = "selected"';
            }
            ?>
            <option {{$selected}} value="{{$reqName}}">{{$reqName}}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="form-group">
        <div class="input-group">
          <?php 
            //dd(request('folder_name'));
            if(request('request_type')){   $request_typeArr = request('request_type'); }
            else{ $request_typeArr = []; }
          ?>
          <select name="request_type[]" value="" class="form-control select2" multiple id="request_types">

            <option value="" @if(count($request_typeArr)==0) selected @endif>-- Select Method --</option>
            <option value="GET" <?php if (in_array('GET', $request_typeArr)) {
                                  echo 'selected';
                                } ?>>GET</option>
            <option value="POST" <?php if (in_array('POST', $request_typeArr)) {
                                    echo 'selected';
                                  } ?>>POST</option>
            <option value="PUT" <?php  if (in_array('PUT', $request_typeArr)) {
                                  echo 'selected';
                                } ?>>PUT</option>
            <option value="PATCH" <?php if (in_array('PATCH', $request_typeArr)) {
                                    echo 'selected';
                                  } ?>>PATCH</option>
            <option value="DELETE" <?php if (in_array("DELETE", $request_typeArr)) {
                                      echo 'selected';
                                    } ?>>DELETE</option>
          </select>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="form-group">
        <div class="input-group">
          <input type="text" placeholder="Search By ID" class="form-control" name="search_id" value="{{request('search_id')}}">
        </div>
      </div>
    </div>
    <div class="col">
      <div class="form-group">
        <div class="input-group">
          <input type="text" placeholder="Search By Keyword" class="form-control" name="keyword" value="{{request('keyword')}}">
        </div>
      </div>
    </div>
    <div class="col">
      <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
      <a href="/postman" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
    </div>
  </form>
  <button type="button" class="btn custom-button float-right mr-3 openmodeladdpostman" data-toggle="modal" data-target="#addPostman">Add Postman request</button>
  <a href="/postman/folder" class="btn custom-button float-right mr-3">Add Folder</a>
  <a href="/postman/workspace" class="btn custom-button float-right mr-3">Add Workspace</a>
  <a href="/postman/collection" class="btn custom-button float-right mr-3">Add Collection</a>
  <button type="button" class="btn custom-button float-right mr-3 openmodeladdpostman" data-toggle="modal" data-target="#status-create">Add Status</button>
<button type="button" class="btn custom-button float-right mr-3" data-toggle="modal" data-target="#postmandatatablecolumnvisibilityList">Column Visiblity</button>
<button class="btn custom-button" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
  <div class="col-12">
    <h3>Assign Permission to User</h3>
    <form class="form-inline" id="update_user_permission" action="/postman/user/permission" method="POST">
      <div class="form-group">
        <div class="input-group">
          <select name="per_folder_name" class="form-control" id="per_folder_name" required>
            <option value="">--select folder for Permission--</option>
            <?php
            $ops = 'id';
            foreach ($folders as $folder) {
              $selected  = '';
              if ($folder->id == request('per_folder_name'))
                $selected  = 'selected';
              echo '<option value="' . $folder->id . '" ' . $selected . '>' . $folder->name . '</option>';
            }
            ?>
          </select>
        </div>
      </div> &nbsp;&nbsp;&nbsp;
      <div class="form-group">
        <div class="input-group">
          <select name="per_user_name" class="form-control" id="per_user_name" required>
            <option value="">--select user for Permission--</option>
            <?php
            foreach ($users as $user) {
              $selected  = '';
              if ($user->id == request('per_user_name'))
                $selected  = 'selected';
              echo '<option value="' . $user->id . '" ' . $selected . '>' . $user->name . '</option>';
            }
            ?>
          </select>
        </div>
      </div> &nbsp;&nbsp;
      <button type="submit" class="btn custom-button update-userpermission">Update User Permission</button>
    </form>
  </div>

</div>

</br>
<div class="row m-0">
  <div class="col-12" style="border: 1px solid;border-color: #dddddd;">
    <div class="table-responsive mt-2" style="overflow-x: auto !important;">

        @if ($message = Session::get('success'))
            <div class="col-lg-12">
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        
      <table class="table table-bordered text-nowrap">
        <thead>
          <tr>
            @if(!empty($dynamicColumnsToShowPostman))
                @if (!in_array('ID', $dynamicColumnsToShowPostman))
                    <th style="width: 3%;">ID</th>
                @endif

                @if (!in_array('Folder Name', $dynamicColumnsToShowPostman))
                    <th style="width: 4%;overflow-wrap: anywhere;">Folder Name</th>
                @endif

                @if (!in_array('PostMan Status', $dynamicColumnsToShowPostman))
                    <th style="width: 25%;overflow-wrap: anywhere;">PostMan Status</th>
                @endif

                @if (!in_array('API Issue Fix Done', $dynamicColumnsToShowPostman))
                    <th style="width: 15%;overflow-wrap: anywhere;">API Issue Fix Done</th>
                @endif

                @if (!in_array('Controller Name', $dynamicColumnsToShowPostman))
                    <th style="width: 5%;overflow-wrap: anywhere;">Controller Name</th>
                @endif

                @if (!in_array('Method Name', $dynamicColumnsToShowPostman))
                    <th style="width: 4%;overflow-wrap: anywhere;">Method Name</th>
                @endif

                @if (!in_array('Request Name', $dynamicColumnsToShowPostman))
                    <th style="width: 4%;overflow-wrap: anywhere;">Request Name</th>
                @endif

                @if (!in_array('Type', $dynamicColumnsToShowPostman))
                    <th style="width: 5%;overflow-wrap: anywhere;">Type</th>
                @endif

                @if (!in_array('URL', $dynamicColumnsToShowPostman))
                    <th style="width: 5%;overflow-wrap: anywhere;">URL</th>
                @endif

                @if (!in_array('Request Parameter', $dynamicColumnsToShowPostman))
                    <th style="width: 5%;overflow-wrap: anywhere;">Request Parameter</th>
                @endif

                @if (!in_array('Params', $dynamicColumnsToShowPostman))
                    <th style="width: 5%;overflow-wrap: anywhere;">Params</th>
                @endif

                @if (!in_array('Headers', $dynamicColumnsToShowPostman))
                    <th style="width: 5%;overflow-wrap: anywhere;">Headers</th>
                @endif

                @if (!in_array('Request type', $dynamicColumnsToShowPostman))
                    <th style="width: 5%;overflow-wrap: anywhere;">Request type</th>
                @endif

                @if (!in_array('Request Response', $dynamicColumnsToShowPostman))
                    <th style="width: 5%;overflow-wrap: anywhere;">Request Response</th>
                @endif

                @if (!in_array('Response Code', $dynamicColumnsToShowPostman))
                    <th style="width: 5%;overflow-wrap: anywhere;">Response Code</th>
                @endif

                @if (!in_array('Grumphp Errors', $dynamicColumnsToShowPostman))
                    <th style="width: 5%;overflow-wrap: anywhere;">Grumphp Errors</th>
                @endif

                @if (!in_array('Magento API Standards', $dynamicColumnsToShowPostman))
                    <th style="width: 5%;overflow-wrap: anywhere;">Magento API Standards</th>
                @endif

                @if (!in_array('Swagger DocBlock', $dynamicColumnsToShowPostman))
                    <th style="width: 5%;overflow-wrap: anywhere;">Swagger DocBlock</th>
                @endif

                @if (!in_array('Used for', $dynamicColumnsToShowPostman))
                    <th style="width: 5%;overflow-wrap: anywhere;">Used for</th>
                @endif

                @if (!in_array('Used in', $dynamicColumnsToShowPostman))
                    <th style="width: 5%;overflow-wrap: anywhere;">Used in</th>
                @endif

                @if (!in_array('Action', $dynamicColumnsToShowPostman))
                    <th style="width: 22%;overflow-wrap: anywhere;">Action</th>
                @endif
            @else 
                <th style="width: 3%;">ID</th>
                <th style="width: 4%;overflow-wrap: anywhere;">Folder Name</th>
                <th style="width: 25%;overflow-wrap: anywhere;">PostMan Status</th>
                <th style="width: 15%;overflow-wrap: anywhere;">API Issue Fix Done</th>
                <th style="width: 5%;overflow-wrap: anywhere;">Controller Name</th>
                <th style="width: 4%;overflow-wrap: anywhere;">Method Name</th>
                <th style="width: 4%;overflow-wrap: anywhere;">Request Name</th>
                <th style="width: 5%;overflow-wrap: anywhere;">Type</th>
                <th style="width: 5%;overflow-wrap: anywhere;">URL</th>
                <th style="width: 5%;overflow-wrap: anywhere;">Request Parameter</th>
                <th style="width: 5%;overflow-wrap: anywhere;">Params</th>
                <th style="width: 5%;overflow-wrap: anywhere;">Headers</th>
                <th style="width: 5%;overflow-wrap: anywhere;">Request type</th>
                <th style="width: 5%;overflow-wrap: anywhere;">Request Response</th>
                <th style="width: 5%;overflow-wrap: anywhere;">Response Code</th>
                <th style="width: 5%;overflow-wrap: anywhere;">Grumphp Errors</th>
                <th style="width: 5%;overflow-wrap: anywhere;">Magento API Standards</th>
                <th style="width: 5%;overflow-wrap: anywhere;">Swagger DocBlock</th>
                <th style="width: 5%;overflow-wrap: anywhere;">Used for</th>
                <th style="width: 5%;overflow-wrap: anywhere;">Used in</th>
                <th style="width: 22%;overflow-wrap: anywhere;">Action</th>
            @endif
          </tr>
        </thead>

        <tbody>
          @foreach ($postmans as $key => $postman)
          @php
            $status_color = \App\Models\PostmanStatus::where('id',$postman->status_id)->first();
            if ($status_color == null) {
                $status_color = new stdClass();
            }
        @endphp
          @php
          $userAccessArr = explode(",",$postman->user_permission);
          array_push($userAccessArr, $addAdimnAccessID)
          @endphp
          @if (in_array($userID, $userAccessArr))
            @if(!empty($dynamicColumnsToShowPostman))
                <tr style="background-color: {{$status_color->postman_color ?? ""}}!important;">
                    @if (!in_array('ID', $dynamicColumnsToShowPostman))
                        <td>{{$postman->id}}</td>
                    @endif

                    @if (!in_array('Folder Name', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="name" data-id="{{$postman->id}}">
                            <span class="show-short-name-{{$postman->id}}">{{ Str::limit($postman->name, 5, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-name-{{$postman->id}} hidden">{{$postman->name}}</span>
                        </td>
                    @endif

                    @if (!in_array('PostMan Status', $dynamicColumnsToShowPostman))
                        <td style="width: 25%;">
                            <div class="d-flex align-items-center">
                                <select name="status" class="status-dropdown" data-id="{{$postman->id}}">
                                    <option value="">Select Status</option>
                                    @foreach ($status as $stat)
                                        <option value="{{$stat->id}}" {{$postman->status_id == $stat->id ? 'selected' : ''}}>{{$stat->status_name}}</option>
                                    @endforeach
                                </select>
                                <button type="button" data-id="{{ $postman->id  }}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
                            </div>
                        </td>
                    @endif

                    @if (!in_array('API Issue Fix Done', $dynamicColumnsToShowPostman))
                        <td style="width: 15%;">
                            <div class="d-flex align-items-center">
                                <select name="api_issue_fix_done" class="api-issue-fix-done-dropdown" data-id="{{$postman->id}}">
                                    <option value="">Select</option>
                                    <option value="0" {{$postman->api_issue_fix_done === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$postman->api_issue_fix_done === 1 ? 'selected' : ''}}>Yes</option>
                                    <option value="2" {{$postman->api_issue_fix_done === 2 ? 'selected' : ''}}>Lead Verified</option>
                                </select>
                                <button type="button" data-id="{{ $postman->id  }}" class="btn btn-image api-issue-fix-done-history-show p-0 ml-2"  title="Api Issue Fix Done Histories" ><i class="fa fa-info-circle"></i></button>
                            </div>
                        </td>
                    @endif
                
                    @if (!in_array('Controller Name', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="controller_name" data-id="{{$postman->id}}">
                            <span class="show-short-controller_name-{{$postman->id}}">{{ Str::limit($postman->controller_name, 5, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-controller_name-{{$postman->id}} hidden">{{$postman->controller_name}}</span>
                        </td>
                    @endif

                    @if (!in_array('Method Name', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="method_name" data-id="{{$postman->id}}">
                            <span class="show-short-method_name-{{$postman->id}}">{{ Str::limit($postman->method_name, 5, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-method_name-{{$postman->id}} hidden">{{$postman->method_name}}</span>
                        </td>
                    @endif

                    @if (!in_array('Request Name', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="request_name" data-id="{{$postman->id}}">
                            <span class="show-short-request_name-{{$postman->id}}">{{ Str::limit($postman->request_name, 5, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-request_name-{{$postman->id}} hidden">{{$postman->request_name}}</span>
                        </td>
                    @endif

                    @if (!in_array('Type', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="request_type" data-id="{{$postman->id}}">
                            <span class="show-short-request_type-{{$postman->id}}">{{ Str::limit($postman->request_type, 5, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-request_type-{{$postman->id}} hidden">{{$postman->request_type}}</span>
                        </td>
                    @endif

                    @if (!in_array('URL', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="url" data-id="{{$postman->id}}">
                            <span class="show-short-url-{{$postman->id}}">{{ Str::limit($postman->request_url, 5, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-url-{{$postman->id}} hidden">{{$postman->request_url}}</span>
                        </td>
                    @endif

                    @if (!in_array('Request Parameter', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="paramiters" data-id="{{$postman->id}}">
                            <span class="show-short-paramiters-{{$postman->id}}">{{ Str::limit($postman->body_json, 5, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-paramiters-{{$postman->id}} hidden">{{$postman->body_json}}</span>
                        </td>
                    @endif

                    @if (!in_array('Params', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="params" data-id="{{$postman->id}}">
                            <span class="show-short-params-{{$postman->id}}">{{ Str::limit($postman->params, 5, '...')}}</span>
                            <span style="word-break:break-all;" class="show-full-params-{{$postman->id}} hidden">{{$postman->params}}</span>
                        </td>
                    @endif

                    @if (!in_array('Headers', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="headers" data-id="{{$postman->id}}">
                            <span class="show-short-headers-{{$postman->id}}">{{ Str::limit($postman->request_headers, 5, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-headers-{{$postman->id}} hidden">{{$postman->request_headers}}</span>
                        </td>
                    @endif

                    @if (!in_array('Request type', $dynamicColumnsToShowPostman))
                        <td>{{$postman->request_type}}</td>
                    @endif

                    @if (!in_array('Request Response', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="response" data-id="{{$postman->id}}">
                            <span class="show-short-response-{{$postman->id}}">{{ Str::limit($postman->response, 12, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-response-{{$postman->id}} hidden">{{$postman->response}}</span>
                        </td>
                    @endif

                    @if (!in_array('Response Code', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="response_code" data-id="{{$postman->id}}">
                            <span class="show-short-response_code-{{$postman->id}}">{{ Str::limit($postman->response_code  , 5, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-response_code-{{$postman->id}} hidden">{{$postman->response_code}}</span>
                        </td>
                    @endif

                    @if (!in_array('Grumphp Errors', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="grumphp_errors" data-id="{{$postman->id}}">
                            <span class="show-short-grumphp_errors-{{$postman->id}}">{{ Str::limit($postman->grumphp_errors  , 8, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-grumphp_errors-{{$postman->id}} hidden">{{$postman->grumphp_errors}}</span>
                        </td>
                    @endif

                    @if (!in_array('Magento API Standards', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="magento_api_standards" data-id="{{$postman->id}}">
                            <span class="show-short-magento_api_standards-{{$postman->id}}">{{ Str::limit($postman->magento_api_standards  , 15, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-magento_api_standards-{{$postman->id}} hidden">{{$postman->magento_api_standards}}</span>
                        </td>
                    @endif

                    @if (!in_array('Swagger DocBlock', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="swagger_doc_block" data-id="{{$postman->id}}">
                            <span class="show-short-swagger_doc_block-{{$postman->id}}">{{ Str::limit($postman->swagger_doc_block  , 15, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-swagger_doc_block-{{$postman->id}} hidden">{{$postman->swagger_doc_block}}</span>
                        </td>
                    @endif

                    @if (!in_array('Used for', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="used_for" data-id="{{$postman->id}}">
                            <span class="show-short-used_for-{{$postman->id}}">{{ Str::limit($postman->used_for  , 5, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-used_for-{{$postman->id}} hidden">{{$postman->used_for}}</span>
                        </td>
                    @endif

                    @if (!in_array('Used in', $dynamicColumnsToShowPostman))
                        <td class="expand-row-msg" data-name="user_in" data-id="{{$postman->id}}">
                            <span class="show-short-user_in-{{$postman->id}}">{{ Str::limit($postman->user_in  , 5, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-user_in-{{$postman->id}} hidden">{{$postman->user_in}}</span>
                        </td>
                    @endif

                    @if (!in_array('Action', $dynamicColumnsToShowPostman))
                        <td>
                            <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$postman->id}}')"><i class="fa fa-arrow-down"></i></button>
                        </td>
                    @endif
                </tr>

                @if (!in_array('Action', $dynamicColumnsToShowPostman))
                    <tr class="action-btn-tr-{{$postman->id}} d-none">
                        <td class="font-weight-bold">Action</td>
                        <td colspan="11" class="cls-actions">
                            <div>
                                <div class="row cls_action_box" style="margin:0px;">
                                    <a title="Send Request" class="btn btn-image abtn-pd postman-list-url-btn postman-send-request-btn1 pd-5 btn-ht" data-id="{{ $postman->id }}" data-toggle="modal" data-target="#postmanmulUrlDetailsModel" href="javascript:;">
                                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-image edit-postman-btn abtn-pd" data-id="{{ $postman->id }}"><img data-id="{{ $postman->id }}" src="/images/edit.png" style="cursor: nwse-resize; width: 16px;"></a>
                                    @if (Auth::user()->isAdmin())
                                    <a class="btn delete-postman-btn abtn-pd padding-top-action" data-id="{{ $postman->id }}" href="#"><img data-id="{{ $postman->id }}" src="/images/delete.png" style="cursor: nwse-resize; width: 16px;"></a>
                                    <a title="Edit History" class="btn abtn-pd preview_edit_history padding-top-action" data-id="{{ $postman->id }}" href="javascript:;"><i class="fa fa-tachometer" aria-hidden="true"></i></a>
                                    @endif
                                    <a class="btn postman-history-btn abtn-pd padding-top-action" data-id="{{ $postman->id }}" href="#"><i class="fa fa-history" aria-hidden="true"></i></a>
                                    <a title="Preview Response" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_response pd-5 btn-ht" href="javascript:;"><i class="fa fa-product-hunt" aria-hidden="true"></i></a>
                                    <a title="Preview Requested" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_requested pd-5 btn-ht" href="javascript:;"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                    <a title="Preview Remark History" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_remark_history pd-5 btn-ht" href="javascript:;"><i class="fa fa-history" aria-hidden="true"></i></a>
                                    <a title="Preview Error" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_postman_error pd-5 btn-ht" href="javascript:;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></a>

                                    <button style="padding:3px;" title="create quick task" type="button" class="btn btn-image d-inline create-quick-task " data-id="@if ($postman) {{ $postman->id }} @endif"  data-category_title="{{$postman->request_name}}" data-title="@if ($postman) {{$postman->request_name }} @endif"><i class="fa fa-plus" aria-hidden="true"></i></button>

                                    <button style="padding-left: 0;padding-left:3px;" type="button" class="btn btn-image d-inline count-dev-customer-tasks" title="Show task history" data-id="@if ($postman) {{ $postman->id }} @endif" data-category="{{ $postman->id }}"><i class="fa fa-info-circle"></i></button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endif
            @else
            <tr style="background-color: {{$status_color->postman_color ?? ""}}!important;">
            <td>{{$postman->id}}</td>
            <td class="expand-row-msg" data-name="name" data-id="{{$postman->id}}">
              <span class="show-short-name-{{$postman->id}}">{{ Str::limit($postman->name, 5, '..')}}</span>
              <span style="word-break:break-all;" class="show-full-name-{{$postman->id}} hidden">{{$postman->name}}</span>
            </td>
            <td style="width: 25%;">
              <div class="d-flex align-items-center">
                <select name="status" class="status-dropdown" data-id="{{$postman->id}}">
                  <option value="">Select Status</option>
                  @foreach ($status as $stat)
                    <option value="{{$stat->id}}" {{$postman->status_id == $stat->id ? 'selected' : ''}}>{{$stat->status_name}}</option>
                  @endforeach
                </select>
                <button type="button" data-id="{{ $postman->id  }}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
              </div>
            </td>
            <td style="width: 15%;">
              <div class="d-flex align-items-center">
                <select name="api_issue_fix_done" class="api-issue-fix-done-dropdown" data-id="{{$postman->id}}">
                  <option value="">Select</option>
                  <option value="0" {{$postman->api_issue_fix_done === 0 ? 'selected' : ''}}>No</option>
                  <option value="1" {{$postman->api_issue_fix_done === 1 ? 'selected' : ''}}>Yes</option>
                  <option value="2" {{$postman->api_issue_fix_done === 2 ? 'selected' : ''}}>Lead Verified</option>
                </select>
                <button type="button" data-id="{{ $postman->id  }}" class="btn btn-image api-issue-fix-done-history-show p-0 ml-2"  title="Api Issue Fix Done Histories" ><i class="fa fa-info-circle"></i></button>
              </div>
            </td>
            
            <td class="expand-row-msg" data-name="controller_name" data-id="{{$postman->id}}">
              <span class="show-short-controller_name-{{$postman->id}}">{{ Str::limit($postman->controller_name, 5, '..')}}</span>
              <span style="word-break:break-all;" class="show-full-controller_name-{{$postman->id}} hidden">{{$postman->controller_name}}</span>
            </td>
            <td class="expand-row-msg" data-name="method_name" data-id="{{$postman->id}}">
              <span class="show-short-method_name-{{$postman->id}}">{{ Str::limit($postman->method_name, 5, '..')}}</span>
              <span style="word-break:break-all;" class="show-full-method_name-{{$postman->id}} hidden">{{$postman->method_name}}</span>
            </td>
            <td class="expand-row-msg" data-name="request_name" data-id="{{$postman->id}}">
              <span class="show-short-request_name-{{$postman->id}}">{{ Str::limit($postman->request_name, 5, '..')}}</span>
              <span style="word-break:break-all;" class="show-full-request_name-{{$postman->id}} hidden">{{$postman->request_name}}</span>
            </td>
            <td class="expand-row-msg" data-name="request_type" data-id="{{$postman->id}}">
              <span class="show-short-request_type-{{$postman->id}}">{{ Str::limit($postman->request_type, 5, '..')}}</span>
              <span style="word-break:break-all;" class="show-full-request_type-{{$postman->id}} hidden">{{$postman->request_type}}</span>
            </td>
            <td class="expand-row-msg" data-name="url" data-id="{{$postman->id}}">
              <span class="show-short-url-{{$postman->id}}">{{ Str::limit($postman->request_url, 5, '..')}}</span>
              <span style="word-break:break-all;" class="show-full-url-{{$postman->id}} hidden">{{$postman->request_url}}</span>
            </td>
            <td class="expand-row-msg" data-name="paramiters" data-id="{{$postman->id}}">
              <span class="show-short-paramiters-{{$postman->id}}">{{ Str::limit($postman->body_json, 5, '..')}}</span>
              <span style="word-break:break-all;" class="show-full-paramiters-{{$postman->id}} hidden">{{$postman->body_json}}</span>
            </td>
            <td class="expand-row-msg" data-name="params" data-id="{{$postman->id}}">
              <span class="show-short-params-{{$postman->id}}">{{ Str::limit($postman->params, 5, '...')}}</span>
              <span style="word-break:break-all;" class="show-full-params-{{$postman->id}} hidden">{{$postman->params}}</span>
            </td>
            <td class="expand-row-msg" data-name="headers" data-id="{{$postman->id}}">
              <span class="show-short-headers-{{$postman->id}}">{{ Str::limit($postman->request_headers, 5, '..')}}</span>
              <span style="word-break:break-all;" class="show-full-headers-{{$postman->id}} hidden">{{$postman->request_headers}}</span>
            </td>
            <td>{{$postman->request_type}}</td>
            <td class="expand-row-msg" data-name="response" data-id="{{$postman->id}}">
              <span class="show-short-response-{{$postman->id}}">{{ Str::limit($postman->response, 12, '..')}}</span>
              <span style="word-break:break-all;" class="show-full-response-{{$postman->id}} hidden">{{$postman->response}}</span>
            </td>
            <td class="expand-row-msg" data-name="response_code" data-id="{{$postman->id}}">
              <span class="show-short-response_code-{{$postman->id}}">{{ Str::limit($postman->response_code  , 5, '..')}}</span>
              <span style="word-break:break-all;" class="show-full-response_code-{{$postman->id}} hidden">{{$postman->response_code}}</span>
            </td>
            <td class="expand-row-msg" data-name="grumphp_errors" data-id="{{$postman->id}}">
              <span class="show-short-grumphp_errors-{{$postman->id}}">{{ Str::limit($postman->grumphp_errors  , 8, '..')}}</span>
              <span style="word-break:break-all;" class="show-full-grumphp_errors-{{$postman->id}} hidden">{{$postman->grumphp_errors}}</span>
            </td>
            <td class="expand-row-msg" data-name="magento_api_standards" data-id="{{$postman->id}}">
              <span class="show-short-magento_api_standards-{{$postman->id}}">{{ Str::limit($postman->magento_api_standards  , 15, '..')}}</span>
              <span style="word-break:break-all;" class="show-full-magento_api_standards-{{$postman->id}} hidden">{{$postman->magento_api_standards}}</span>
            </td>
            <td class="expand-row-msg" data-name="swagger_doc_block" data-id="{{$postman->id}}">
              <span class="show-short-swagger_doc_block-{{$postman->id}}">{{ Str::limit($postman->swagger_doc_block  , 15, '..')}}</span>
              <span style="word-break:break-all;" class="show-full-swagger_doc_block-{{$postman->id}} hidden">{{$postman->swagger_doc_block}}</span>
            </td>
            <td class="expand-row-msg" data-name="used_for" data-id="{{$postman->id}}">
              <span class="show-short-used_for-{{$postman->id}}">{{ Str::limit($postman->used_for  , 5, '..')}}</span>
              <span style="word-break:break-all;" class="show-full-used_for-{{$postman->id}} hidden">{{$postman->used_for}}</span>
            </td>
            <td class="expand-row-msg" data-name="user_in" data-id="{{$postman->id}}">
              <span class="show-short-user_in-{{$postman->id}}">{{ Str::limit($postman->user_in  , 5, '..')}}</span>
              <span style="word-break:break-all;" class="show-full-user_in-{{$postman->id}} hidden">{{$postman->user_in}}</span>
            </td>
            <td>
              <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$postman->id}}')"><i class="fa fa-arrow-down"></i></button>
            </td>
          </tr>
          <tr class="action-btn-tr-{{$postman->id}} d-none">
            <td class="font-weight-bold">Action</td>
            <td colspan="11" class="cls-actions">
                <div>
                    <div class="row cls_action_box" style="margin:0px;">
                      <a title="Send Request" class="btn btn-image abtn-pd postman-list-url-btn postman-send-request-btn1 pd-5 btn-ht" data-id="{{ $postman->id }}" data-toggle="modal" data-target="#postmanmulUrlDetailsModel" href="javascript:;">
                        <i class="fa fa-paper-plane" aria-hidden="true"></i>
                      </a>
                      <a class="btn btn-image edit-postman-btn abtn-pd" data-id="{{ $postman->id }}"><img data-id="{{ $postman->id }}" src="/images/edit.png" style="cursor: nwse-resize; width: 16px;"></a>
                      @if (Auth::user()->isAdmin())
                      <a class="btn delete-postman-btn abtn-pd padding-top-action" data-id="{{ $postman->id }}" href="#"><img data-id="{{ $postman->id }}" src="/images/delete.png" style="cursor: nwse-resize; width: 16px;"></a>
                      <a title="Edit History" class="btn abtn-pd preview_edit_history padding-top-action" data-id="{{ $postman->id }}" href="javascript:;"><i class="fa fa-tachometer" aria-hidden="true"></i></a>
                      @endif
                      <a class="btn postman-history-btn abtn-pd padding-top-action" data-id="{{ $postman->id }}" href="#"><i class="fa fa-history" aria-hidden="true"></i></a>
                      <a title="Preview Response" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_response pd-5 btn-ht" href="javascript:;"><i class="fa fa-product-hunt" aria-hidden="true"></i></a>
                      <a title="Preview Requested" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_requested pd-5 btn-ht" href="javascript:;"><i class="fa fa-eye" aria-hidden="true"></i></a>
                      <a title="Preview Remark History" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_remark_history pd-5 btn-ht" href="javascript:;"><i class="fa fa-history" aria-hidden="true"></i></a>
                      <a title="Preview Error" data-id="{{ $postman->id }}" class="btn btn-image abtn-pd preview_postman_error pd-5 btn-ht" href="javascript:;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></a>

                      <button style="padding:3px;" title="create quick task" type="button" class="btn btn-image d-inline create-quick-task " data-id="@if ($postman) {{ $postman->id }} @endif"  data-category_title="{{$postman->request_name}}" data-title="@if ($postman) {{$postman->request_name }} @endif"><i class="fa fa-plus" aria-hidden="true"></i></button>
                    </div>
                </div>
            </td>
            </tr>
            @endif        
          @endif

          @endforeach
        </tbody>
      </table>
      <div class="text-center">
        {!! $postmans->appends(Request::except('page'))->links() !!}
      </div>
    </div>
  </div>
  <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
  </div>
</div>
@endsection
 <!-- Stuatus Create  Modal content-->
 <div id="status-create" class="modal fade in" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Add Stauts</h4>
      <button type="button" class="close" data-dismiss="modal">×</button>
      </div>
      <form  method="POST" id="status-create-form">
        @csrf
        @method('POST')
          <div class="modal-body">
            <div class="form-group">
              {!! Form::label('status_name', 'Name', ['class' => 'form-control-label']) !!}
              {!! Form::text('status_name', null, ['class'=>'form-control','required','rows'=>3]) !!}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary status-save-btn">Save</button>
          </div>
        </div>
      </form>
    </div>

  </div>
</div>

<div id="postmanHistory" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Postman History</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Updated by</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody class="tbodayPostmanHistory">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="postmanRequesteHistoryModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Postman Request History</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User Name</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody class="tbodayPostmanRequestHistory">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="postmanRemarkHistoryModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Postman Remark History</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User Name</th>
                  <th>Old Remark</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody class="tbodayPostmanRemarkHistory">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="postmanUserDetailsModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">URLs</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body postmanUserDetailsModelBody">

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="postmanUrlDetailsModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Users</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" id="multiUrls">
              <div class="postmanUrlDetailsBody"></div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary postman-send-request-btn">Send</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="postmanResponseHistoryModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Postman Response History</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User Name</th>
                  <th>Response</th>
                  <th>Response code</th>
                  <th>Request</th>
                  <th>Parameters</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody class="tbodayPostmanResponseHistory">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="postmanEditHistoryModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Postman Edit History</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User Name</th>
                  <th>Parmiters</th>
                  <th>Urls</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody class="tbodayPostmanEditHistory">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="postmanErrorModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">Postman Error History</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>Error Type</th>
                    <th>Error</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody class="tbodayPostmanErrorHistory">
                </tbody>
              </table>  
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

<div id="addPostman" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">

        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><span id="titleUpdate">Add</span> Postman Request</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="postmanform" method="post" class="mb-0">
              @csrf
              <div class="form-row">
                <input type="hidden" id="post_id" name="id" value="" />
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="title">User Name</label>
                    <div class="dropdown-sin-1 postman-dropdown-display">
                      {{-- <select style="display:none" multiple placeholder="Select"></select> multiselect --}}

                      <select name="user_permission[]" multiple class="folder_name form-control dropdown-mul-1" id="user_permission" required >
                        <option>--Users--</option>
                        <?php
                        foreach ($users as $user) {
                          echo '<option value="' . $user->id . '" data-folder_name="' . $user->name . '">' . $user->name . '</option>';
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="title">Folder Name</label>
                    <input type="hidden" name="folder_real_name" id="folder_real_name">
                    <select name="folder_name" class="form-control folder_name" id="folder_name" required>
                      <option>--Folder--</option>
                      <?php
                      $ops = 'id';
                      foreach ($folders as $folder) {
                        echo '<option value="' . $folder->id . '" data-folder_name="' . $folder->name . '">' . $folder->name . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="request_name">Request Name</label>
                    <input type="text" name="request_name" value="" class="form-control" id="request_name" placeholder="Enter request name">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="request_types">Request Type</label>
                    <select name="request_types" value="" class="form-control" id="request_types">
                      <option value="GET">GET</option>
                      <option value="POST">POST</option>
                      <option value="PUT">PUT</option>
                      <option value="PATCH">PATCH</option>
                      <option value="DELETE">DELETE</option>
                    </select>
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-12">
                    <label for="request_url">Request Url</label>
                    <div class="form-group add_more_urls_div">
                      <input type="text" name="request_url[]" value="" class="form-control" id="request_url" placeholder="Enter request url">
                    </div>
                    <br>
                    <a style="cursor: pointer;" class="add_more_urls"><i class="fa fa-plus"> Add more</i></a>
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="controller_name">Controller Name</label>
                    <input type="text" name="controller_name" value="" class="form-control" id="controller_name" placeholder="Enter Controller Name">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="method_name">Method Name</label>
                    <input type="text" name="method_name" value="" class="form-control" id="method_name" placeholder="Enter Method Name">
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="remark">Remark</label>
                    <input type="text" name="remark" value="" class="form-control" id="remark" placeholder="Enter Remark">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="params">Params</label>
                    <textarea name="params" value="" class="form-control" id="params" placeholder="Enter params ex. filedName1: value1, filedName2: value2" style="height: 34px;"></textarea>
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="authorization_type">Authorization type</label>
                    <select name="authorization_type" value="" class="form-control" id="authorization_type">
                      <option value="Bearer Token">Bearer Token</option>
                      <option value="Basic Auth">Basic Auth</option>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="authorization_token">Authorization token</label>
                    <input type="text" name="authorization_token" value="" class="form-control" id="authorization_token" placeholder="Enter authorization token">
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="request_headers">headers</label>
                    <textarea name="request_headers" value="" class="form-control" id="request_headers" placeholder="Enter headers ex. filedName1: value1, filedName2: value2" style="height: 34px;"></textarea>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="body_type">Body type</label>
                    <select name="body_type" value="" class="form-control" id="body_type">
                      <option value="raw">Raw</option>
                    </select>
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="body_json" class="label-btn">Body Json
                      <button type="button" class="btn custom-button float-right mr-3 add-json" data-toggle="modal" data-target="#addPostmanJsonModel">Add Json</button>
                    </label>
                    <?php
                    $postJsonVer = \App\PostmanRequestJsonHistory::all();
                    ?>
                    <select name="body_json" value="" class="form-control" id="body_json">
                      <option value="">select Json</option>
                      @foreach ($postJsonVer as $jsonVer)
                      <?php $name = $jsonVer->json_Name ?? substr($jsonVer->request_data, 0, 60); ?>
                      <option value="{{$jsonVer->request_data}}">{{$jsonVer->version_json.' '.$name}}</option>
                      @endforeach
                    </select>
                    {{-- <input type="text" name="body_json" value="" class="form-control" id="body_json" placeholder="Enter body json Ex.  {'name': 'hello', 'type':'not'}"> --}}
                  </div>
                  <div class="form-group col-md-6">
                    <label for="pre_request_script">Pre request script</label>
                    <textarea name="pre_request_script" value="" class="form-control" id="pre_request_script" placeholder="Enter pre_request_script" style="height: 34px;"></textarea>
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="tests">Tests</label>
                    <input type="text" name="tests" value="" class="form-control" id="tests" placeholder="Enter tests">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="end_point">End Point</label>
                    <input type="text" name="end_point" value="" class="form-control" id="end_point" placeholder="Enter end point">
                  </div>
                </div>

                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="grumphp_errors">Grumphp Errors</label>
                    <input type="text" name="grumphp_errors" value="" class="form-control" id="grumphp_errors" placeholder="Enter Grumphp Errors">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="magento_api_standards">Magento API Standards</label>
                    <input type="text" name="magento_api_standards" value="" class="form-control" id="magento_api_standards" placeholder="Enter Magento API Standards">
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="swagger_doc_block">Swagger DocBlock</label>
                    <input type="text" name="swagger_doc_block" value="" class="form-control" id="swagger_doc_block" placeholder="Enter Swagger DocBlock">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="used_for">Used for</label>
                    <input type="text" name="used_for" value="" class="form-control" id="used_for" placeholder="Enter Used for">
                  </div>
                </div>
                <div class="form-group col-md-12 mb-0">
                  <div class="form-group col-md-6">
                    <label for="user_in">Used in</label>
                    <select name="user_in" value="" class="form-control" id="user_in">
                      <option value="ERP">ERP</option>
                      <option value="Mobile">Mobile</option>
                      <option value="Both">Both</option>
                      <option value="Unknown">Unknown</option>
                    </select>
                  </div>
                </div>

              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-secondary submit-form">Save</button>
          </div>
          <div id="addPostman" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
              <!-- Modal content-->
              <div class="modal-content ">
                <div id="add-mail-content">

                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title"><span id="titleUpdate">Add</span> Postman Request</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <form id="postmanform" method="post">
                        @csrf
                        <div class="form-row">
                          <input type="hidden" id="post_id" name="id" value="" />

                          <div class="form-group col-md-12">
                            <label for="title">User Name</label>
                            <input type="hidden" name="folder_real_name" id="folder_real_name">
                            <div class="dropdown-sin-1">
                              {{-- <select style="display:none" multiple placeholder="Select"></select> multiselect --}}
                              <select name="user_permission[]" multiple class="form-control folder_name dropdown-mul-1" id="user_permission" required>
                                <option>--Users--</option>
                                <?php
                                foreach ($users as $user) {
                                  echo '<option value="' . $user->id . '" data-folder_name="' . $user->name . '">' . $user->name . '</option>';
                                }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="title">Folder Name</label>
                            <input type="hidden" name="folder_real_name" id="folder_real_name">
                            <select name="folder_name" class="form-control folder_name" id="folder_name" required>
                              <option>--Folder--</option>
                              <?php
                              $ops = 'id';
                              foreach ($folders as $folder) {
                                echo '<option value="' . $folder->id . '" data-folder_name="' . $folder->name . '">' . $folder->name . '</option>';
                              }
                              ?>
                            </select>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="request_name">Request Name</label>
                            <input type="text" name="request_name" value="" class="form-control" id="request_name" placeholder="Enter request name">
                          </div>
                          <div class="form-group col-md-12">
                            <label for="request_types">Request Type</label>
                            <select name="request_types" value="" class="form-control" id="request_types">
                              <option value="GET">GET</option>
                              <option value="POST">POST</option>
                              <option value="PUT">PUT</option>
                              <option value="PATCH">PATCH</option>
                              <option value="DELETE">DELETE</option>
                            </select>
                          </div>
                          <div class="form-group col-md-12 ">
                            <label for="request_url">Request Url</label>
                            <div class="form-group  add_more_urls_div">
                              <input type="text" name="request_url[]" value="" class="form-control request_url" id="request_url" placeholder="Enter request url">
                            </div>
                            <br>
                            <a style="cursor: pointer;" class="add_more_urls"><i class="fa fa-plus">Add more</i></a>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="controller_name">Controller Name</label>
                            <input type="text" name="controller_name" value="" class="form-control" id="controller_name" placeholder="Enter Controller Name">
                          </div>
                          <div class="form-group col-md-12">
                            <label for="method_name">Method Name</label>
                            <input type="text" name="method_name" value="" class="form-control" id="method_name" placeholder="Enter Method Name">
                          </div>
                          <div class="form-group col-md-12">
                            <label for="remark">Remark</label>
                            <input type="text" name="remark" value="" class="form-control" id="remark" placeholder="Enter Remark">
                          </div>
                          <div class="form-group col-md-12">
                            <label for="params">Params</label>
                            <textarea name="params" value="" class="form-control" id="params" placeholder="Enter params ex. filedName1: value1, filedName2: value2"></textarea>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="authorization_type">Authorization type</label>
                            <select name="authorization_type" value="" class="form-control" id="authorization_type">
                              <option value="Bearer Token">Bearer Token</option>
                              <option value="Basic Auth">Basic Auth</option>
                            </select>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="authorization_token">Authorization token</label>
                            <input type="text" name="authorization_token" value="" class="form-control" id="authorization_token" placeholder="Enter authorization token">
                          </div>
                          <div class="form-group col-md-12">
                            <label for="request_headers">headers</label>
                            <textarea name="request_headers" value="" class="form-control" id="request_headers" placeholder="Enter headers ex. filedName1: value1, filedName2: value2"></textarea>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="body_type">Body type</label>
                            <select name="body_type" value="" class="form-control" id="body_type">
                              <option value="raw">Raw</option>
                            </select>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="body_json">Body Json
                              <a href="#" id="addJson"> Add</a>
                            </label>
                            <?php
                            $postJsonVer = \App\PostmanRequestJsonHistory::all();
                            ?>
                            <select name="body_json" class="form-control" id="body_json">
                              <option value="">select Json</option>
                              @foreach ($postJsonVer as $jsonVer)
                              <option value="{{$jsonVer->request_data}}">{{$jsonVer->version_json.' '.$jsonVer->request_data}}</option>
                              @endforeach
                            </select>
                            {{-- <input type="text" name="body_json" value="" class="form-control" id="body_json" placeholder="Enter body json Ex.  {'name': 'hello', 'type':'not'}"> --}}
                          </div>
                          <div class="form-group col-md-12">
                            <label for="pre_request_script">Pre request script</label>
                            <textarea name="pre_request_script" value="" class="form-control" id="pre_request_script" placeholder="Enter pre_request_script"></textarea>
                          </div>
                          <div class="form-group col-md-12">
                            <label for="tests">Tests</label>
                            <input type="text" name="tests" value="" class="form-control" id="tests" placeholder="Enter tests">
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
        </div>

      </div>
    </div>
  </div>
</div>

<div id="create-quick-task" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo route('task.create.multiple.task.shortcut'); ?>" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Create Task</h4>
                </div>
                <div class="modal-body">

                    <input class="form-control" value="49" type="hidden" name="category_id" />
                    <input class="form-control" value="" type="hidden" name="category_title" id="category_title" />
                    <input class="form-control" type="hidden" name="site_id" id="site_id" />
                    <div class="form-group">
                        <label for="">Subject</label>
                        <input class="form-control" type="text" id="hidden-task-subject" name="task_subject" />
                    </div>
                    <div class="form-group">
                        <select class="form-control" style="width:100%;" name="task_type" tabindex="-1" aria-hidden="true">
                            <option value="0">Other Task</option>
                            <option value="4">Developer Task</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="repository_id">Repository:</label>
                        <br>
                        <select style="width:100%" class="form-control  " id="repository_id" name="repository_id">
                            <option value="">-- select repository --</option>
                            @foreach (\App\Github\GithubRepository::all() as $repository)
                            <option value="{{ $repository->id }}">{{ $repository->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Details</label>
                        <input class="form-control text-task-development" type="text" name="task_detail" />
                    </div>

                    <div class="form-group">
                        <label for="">Cost</label>
                        <input class="form-control" type="text" name="cost" />
                    </div>

                    <div class="form-group">
                        <label for="">Assign to</label>
                        <select name="task_asssigned_to" class="form-control assign-to select2">
                            @foreach ($allUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Create Review Task?</label>
                        <div class="form-group">
                            <input type="checkbox" name="need_review_task" value="1" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Websites</label>
                        <div class="form-group website-list row">
                           
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-default create-task">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="dev_task_statistics" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Dev Task statistics</h2>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body" id="dev_task_statistics_content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Task type</th>
                                <th>Task Id</th>
                                <th>Assigned to</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addPostmanJsonModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">

        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Json</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="postmanform" method="post">
              @csrf
              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="jsonVersion">Tests</label>
                  <input type="text" name="jsonVersion" required value="" class="form-control" id="jsonVersion" placeholder="Enter Json Here">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="jsonName">Name</label>
                  <input type="text" name="jsonName" required value="" class="form-control" id="jsonName" placeholder="Enter Json Name">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-secondary postman-addJson">Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="view-domain" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="view-domain-content">

      </div>
    </div>
  </div>
</div>

<div id="postmanShowFullTextModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Full text view</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body postmanShowFullTextBody">
            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{{-- /var/www/html/erp/resources/views/postman/postman-status-history.blade.php --}}
@include('postman.postman-status-history')
@include('postman.postman-api-issue-fix-done-history')
@include("postman.column-visibility-modal")
@include("postman.partials.modal-status-color")
<link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dropdown.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dropdown.css')}}">
@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="/js/bootstrap-multiselect.min.js"></script>

<script src="{{asset('js/mock.js')}}"></script>
<script src="{{asset('js/jquery.dropdown.min.js')}}"></script>
<script src="{{asset('js/jquery.dropdown.js')}}"></script>


<script>
  var Random = Mock.Random;
  var json1 = Mock.mock({
    "data|10-50": [{
      name: function() {
        return Random.name(true)
      },
      "id|+1": 1,
      "disabled|1-2": true,
      groupName: 'Group Name',
      "groupId|1-4": 1,
      "selected": true
    }]
  });
  $('.dropdown-mul-1').dropdown({
    data: json1.data,
    limitCount: 40,
    multipleMode: 'label',
    choice: function() {
      // console.log(arguments,this);
    }
  });

  $('.dropdown-sin-1').dropdown({
    readOnly: true,
    input: '<input type="text" maxLength="20" placeholder="Search">'
  });
</script>
</div>

<script type="text/javascript">
    function Showactionbtn(id){
        $(".action-btn-tr-"+id).toggleClass('d-none')
    }
  // $('ul.pagination').hide();
  //   $('.infinite-scroll').jscroll({
  //     autoTrigger: true,
  //     // debug: true,
  //     loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
  //     padding: 0,
  //     nextSelector: '.pagination li.active + li a',
  //     contentSelector: 'div.infinite-scroll',
  //     callback: function () {
  //       $('ul.pagination').first().remove();
  //       $('ul.pagination').hide();
  //     }
  // });

  $('.multiselect').multiselect({
    enableClickableOptGroups: true
  });
  $(document).on("click", ".openmodeladdpostman", function(e) {
    $('#titleUpdate').html("Add");
    $('.add_more_urls_div').html('');
    $('.add_more_urls_div').append('<input type="text" name="request_url[]" value="" class="form-control" id="request_url" placeholder="Enter request url">');
    $('#postmanform').find("input[type=text], textarea").val("");
  });
  $(document).on("click", ".add_more_urls", function(e) {
    $('.add_more_urls_div').append('<input type="text" name="request_url[]" value="" class="form-control" id="request_url" placeholder="Enter request url">');
  });

  $(document).on("click", "#see_users", function(e) {
    e.preventDefault();
    //debugger;
    var $this = $(this);
    var id = $this.data('user_details');
    $('.postmanUserDetailsModelBody').html(id);
  });

  $(document).on("change", ".folder_name", function(e) {
    e.preventDefault();
    var folder_name = $(this).find(':selected').attr('data-folder_name');
    //debugger;
    $('#folder_real_name').val(folder_name);
  });

  $(document).on("click", ".delete-postman-btn", function(e) {
    e.preventDefault();
    if (confirm("Are you sure?")) {
      var $this = $(this);
      var id = $this.data('id');
      $.ajax({
        url: "/postman/delete",
        type: "delete",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          id: id
        }
      }).done(function(response) {
        if (response.code = '200') {
          toastr['success']('Postman deleted successfully!!!', 'success');
          location.reload();
        } else {
          toastr['error'](response.message, 'error');
        }
      }).fail(function(errObj) {
        $('#loading-image').hide();
        $("#addPostman").hide();
        toastr['error'](errObj.message, 'error');
      });
    }
  });
  $(document).on("click", ".submit-form", function(e) {
    e.preventDefault();
    var $this = $(this);
    if ($('#titleUpdate').text() == 'Add')
      $("#post_id").val("");
    $.ajax({
      url: "/postman/create/",
      type: "post",
      data: $('#postmanform').serialize()
    }).done(function(response) {
      if (response.code = '200') {
        $('#loading-image').hide();
        $('#addPostman').modal('hide');
        toastr['success']('Postman added successfully!!!', 'success');
        location.reload();
      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      //$("#addMail").hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", ".edit-postman-btn", function(e) {
    e.preventDefault();
    $('#titleUpdate').html("Update");
    var $this = $(this);
    var id = $this.data('id');
    $.ajax({
      url: "/postman/edit/",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        id: id
      }
    }).done(function(response) {
      if (response.code = '200') {
        form = $('#postmanform');
        $.each(response.data, function(key, v) {
          if (form.find('[name="' + key + '"]').length) {
            form.find('[name="' + key + '"]').val(v);
          } else if (key == 'request_type') {
            form.find('[name="' + key + 's"]').val(v);
          } else if (key == 'request_url') {
            //form.find('[name="'+key+'[]"]').val(v);
            $('.add_more_urls_div').html('');
            $.each(response.postmanUrl, function(i, e) {
              $('.add_more_urls_div').append('<br/><input type="text" name="request_url[]" value="' + e.request_url + '" class="form-control" id="request_url" placeholder="Enter request url">');
            });
          } else if (key == 'folder_name') {
            $("#folder_name").val(v);
          } else if (form.find('[name="' + key + '[]"]').length) {
            form.find('[name="' + key + '[]"]').val(v);
            //debugger;
            $.each(v.split(","), function(i, e) {
              $("#user_permission option[value='" + e + "']").prop("selected", true);
            });
          }
        });
        $("#folder_name").html(response.ops);
        $('#addPostman').modal('show');
        toastr['success']('Postman added successfully!!!', 'success');

      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      $("#addPostman").hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", ".postman-history-btn", function(e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.data('id');
    $.ajax({
      url: "/postman/history/",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        id: id
      }
    }).done(function(response) {
      if (response.code = '200') {
        var t = '';
        $.each(response.data, function(key, v) {
          t += '<tr><td>' + v.id + '</td>';
          t += '<td>' + v.userName + '</td>';
          t += '<td>' + v.created_at + '</td></tr>';
        });
        $(".tbodayPostmanHistory").html(t);
        $('#postmanHistory').modal('show');
        toastr['success']('Postman added successfully!!!', 'success');

      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      $("#postmanHistory").hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", ".postman-list-url-btn", function(e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.data('id');
    $.ajax({
      url: "/postman/get/mul/request",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        id: id
      }
    }).done(function(response) {
      if (response.code = '200') {
        $(".postmanUrlDetailsBody").html('');
        $(".postmanUrlDetailsBody").html(response.data);
        $('#postmanUrlDetailsModel').modal('show');
        toastr['success']('Postman Url listed successfully!!!', 'success');
      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", ".postman-send-request-btn", function(e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.data('id');

    $.ajax({
      url: "/postman/send/request",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: $('#multiUrls').serialize()
    }).done(function(response) {
      if (response.code = '200') {
        toastr['success']('Postman requested successfully!!!', 'success');
      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      toastr['error'](errObj.message, 'error');
    });
  });

    $(document).on("click",".preview_response",function(e){
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        $.ajax({
          url: "/postman/response/history/",
          type: "post",
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
          data:{
            id:id
          }
        }).done(function(response) {
          if(response.code = '200') {
            var t = '';
            $.each(response.data, function(key, v) {
              var responseString  = '';
              if(v.response)
                responseString = v.response.substring(0,10);
              var request_data_val  = '';
              if(v.request_data)
                request_data_val = v.request_data.substring(0,10);
              var request_url_val  = '';
              if(v.request_data)
              request_url_val = v.request_url.substring(0,10)


              t += '<tr><td>'+v.id+'</td>';
              t += '<td>'+v.userName+'</td>';
              t += '<td  class="expand-row-msg" data-name="response" data-id="'+v.id+'" ><span class="show-short-response-'+v.id+'">'+responseString+'...</span>    <span style="word-break:break-all;" class="show-full-response-'+v.id+' hidden">'+v.response+'</span></td>';
              t += '<td>'+v.response_code+'</td>';
              t += '<td  class="expand-row-msg" data-name="request_url" data-id="'+v.id+'" ><span class="show-short-request_url-'+v.id+'">'+request_url_val+'...</span>    <span style="word-break:break-all;" class="show-full-request_url-'+v.id+' hidden">'+v.request_url+'</span></td>';
              t += '<td  class="expand-row-msg" data-name="request_data" data-id="'+v.id+'" ><span class="show-short-request_data-'+v.id+'">'+request_data_val+'...</span>    <span style="word-break:break-all;" class="show-full-request_data-'+v.id+' hidden">'+v.request_data+'</span></td>';
              t += '<td>'+v.created_at+'</td></tr>';
            });
            $(".tbodayPostmanResponseHistory").html(t);
            $('#postmanResponseHistoryModel').modal('show');
            toastr['success']('Postman response listed successfully!!!', 'success'); 
            
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           $("#postmanResponseHistory").hide();
           toastr['error'](errObj.message, 'error');
        });
    });

    
    $(document).on("click",".preview_postman_error",function(e){
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        $.ajax({
          url: "/postman/get/error/history",
          type: "post",
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
          data:{
            id:id
          }
        }).done(function(response) {
          if(response.code = '200') {
            var t = '';
            $.each(response.data, function(key, v) {
              var parent_id_type  = '';
              if(v.parent_id_type)
                parent_id_type = v.parent_id_type.substring(0,10);
              var error  = '';
              if(v.error)
              error = v.error.substring(0,10)


              t += '<tr><td>'+v.id+'</td>';
              t += '<td>'+v.userName+'</td>';
              t += '<td  class="expand-row-msg" data-name="type" data-id="'+v.id+'" ><span class="show-short-type-'+v.id+'">'+parent_id_type+'...</span>    <span style="word-break:break-all;" class="show-full-type-'+v.id+' hidden">'+v.parent_id_type+'</span></td>';
              t += '<td  class="expand-row-msg" data-name="error" data-id="'+v.id+'" ><span class="show-short-error-'+v.id+'">'+error+'...</span>    <span style="word-break:break-all;" class="show-full-error-'+v.id+' hidden">'+v.error+'</span></td>';
              t += '<td>'+v.created_at+'</td></tr>';
            });
            $(".tbodayPostmanErrorHistory").html(t);
            $('#postmanErrorModel').modal('show');
            toastr['success']('Postman Error history listed successfully!!!', 'success'); 
            
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           $("#postmanErrorModel").hide();
           toastr['error'](errObj.message, 'error');
        });
    });
    

  $(document).on("click", ".preview_edit_history", function(e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.data('id');
    $.ajax({
      url: "/postman/edit/history/",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        id: id
      }
    }).done(function(response) {
      if (response.code = '200') {
        var t = '';
        $.each(response.data, function(key, v) {
          var body_json_data = '';
          if (v.body_json)
            body_json_data = v.body_json.substring(0, 10);
          var request_data_val = '';
          if (v.request_data)
            request_data_val = v.request_data.substring(0, 10);
          var request_url_val = '';
          if (v.request_url)
            request_url_val = v.request_url.substring(0, 10)


          t += '<tr><td>' + v.id + '</td>';
          t += '<td>' + v.userName + '</td>';
          t += '<td  class="expand-row-msg" data-name="body_json" data-id="' + v.id + '" ><span class="show-short-body_json-' + v.id + '">' + body_json_data + '...</span>    <span style="word-break:break-all;" class="show-full-body_json-' + v.id + ' hidden">' + v.body_json + '</span></td>';
          //t += '<td>'+v.response_code+'</td>';
          t += '<td  class="expand-row-msg" data-name="request_url" data-id="' + v.id + '" ><span class="show-short-request_url-' + v.id + '">' + request_url_val + '...</span>    <span style="word-break:break-all;" class="show-full-request_url-' + v.id + ' hidden">' + v.request_url + '</span></td>';
          //t += '<td  class="expand-row-msg" data-name="request_data" data-id="'+v.id+'" ><span class="show-short-request_data-'+v.id+'">'+request_data_val+'...</span>    <span style="word-break:break-all;" class="show-full-request_data-'+v.id+' hidden">'+v.request_data+'</span></td>';
          t += '<td>' + v.created_at + '</td></tr>';
        });
        $(".tbodayPostmanEditHistory").html(t);
        $('#postmanEditHistoryModel').modal('show');
        toastr['success']('Postman Edit History listed successfully!!!', 'success');

      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      $("#postmanResponseHistory").hide();
      toastr['error'](errObj.message, 'error');
    });
  });


  $(document).on("click", ".preview_requested", function(e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.data('id');
    $.ajax({
      url: "/postman/requested/history/",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        id: id
      }
    }).done(function(response) {
      if (response.code = '200') {
        var t = '';
        $.each(response.data, function(key, v) {
          t += '<tr><td>' + v.id + '</td>';
          t += '<td>' + v.userName + '</td>';
          t += '<td>' + v.created_at + '</td></tr>';
        });
        $(".tbodayPostmanRequestHistory").html(t);
        $('#postmanRequesteHistoryModel').modal('show');
        toastr['success']('Postman Requeste listed successfully!!!', 'success');

      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      $("#postmanRequesteHistory").hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", ".preview_remark_history", function(e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.data('id');
    $.ajax({
      url: "/postman/remark/history/",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        id: id
      }
    }).done(function(response) {
      if (response.code = '200') {
        var t = '';
        $.each(response.data, function(key, v) {
          t += '<tr><td>' + v.id + '</td>';
          t += '<td>' + v.userName + '</td>';
          t += '<td>' + v.old_remark + '</td>';
          t += '<td>' + v.created_at + '</td></tr>';
        });
        $(".tbodayPostmanRemarkHistory").html(t);
        $('#postmanRemarkHistoryModel').modal('show');
        toastr['success']('Postman Remark History listed successfully!!!', 'success');

      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      $("#postmanRemarkHistoryModel").hide();
      toastr['error'](errObj.message, 'error');
    });
  });
  $(document).on("click", "addJson", function(e) {
    $('#addPostmanJsonModel').modal('show');
  });
  $(document).on("click", ".postman-addJson", function(e) {
    e.preventDefault();
    var jsonData = $('#jsonVersion').val();
    var jsonName = $('#jsonName').val();
    $.ajax({
      url: "postman/add/json/version",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        json_data: jsonData,
        json_name: jsonName
      }
    }).done(function(response) {
      if (response.code = '200') {
        $('#body_json').append(`<option value='${response.data.request_data}'>
                                       ${response.data.version_json+' '+response.data.json_Name}
                                  </option>`);
        toastr['success']('Json Added successfully!!!', 'success');
      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", ".removeuser", function(e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.data('id');
    var user_id = $this.data('user_permision_id');
    $.ajax({
      url: "postman/removeuser/permission",
      type: "post",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        id: id,
        user_id: user_id
      }
    }).done(function(response) {
      if (response.code = '200') {
        $("#" + id + user_id).css('display', 'none');
        toastr['success']('User Removed successfully!!!', 'success');
      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      toastr['error'](errObj.message, 'error');
    });
  });

  $(document).on("click", ".update-userpermission", function(e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.data('id');
    var per_folder_name = $('#per_folder_name').val();
    var per_user_name = $('#per_user_name').val();
    if (per_folder_name && per_user_name) {
      $.ajax({
        url: "{{route('postman.permission')}}",
        type: "post",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          per_folder_name: per_folder_name,
          per_user_name: per_user_name
        }

      }).done(function(response) {
        $('#loading-image').hide();
        if (response.code = '200') {
          toastr['success'](response.message, 'success');
          location.reload();
        } else {
          toastr['error'](response.message, 'error');
        }
      }).fail(function(errObj) {
        $('#loading-image').hide();
        toastr['error'](errObj.message, 'error');
      });
    } else {
      if (per_folder_name == '')
        $('#per_folder_name').addClass("alert alert-danger");
      if (per_user_name == '')
        $('#select2-per_user_name-container').addClass("alert alert-danger");
      setTimeout(function() {
        $('#per_folder_name').removeClass("alert alert-danger");
        $('#select2-per_user_name-container').removeClass("alert alert-danger");
      }, 1000);
      toastr['error']("Please Select Required fileds", 'error');
    }
  });

  $(document).on('click', '.expand-row-msg', function() {
    $('#postmanShowFullTextModel').modal('toggle');
    $(".postmanShowFullTextBody").html("");
    var id = $(this).data('id');
    var name = $(this).data('name');
    var full = '.expand-row-msg .show-full-' + name + '-' + id;
    var fullText = $(full).html();
    $(".postmanShowFullTextBody").html(fullText);
  });

  /*$(document).on('click', '.expand-row-msg', function() {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-' + name + '-' + id;
    var mini = '.expand-row-msg .show-full-' + name + '-' + id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
  }); */
  $(document).ready(function() {
    $(".select2").select2();
    $('#per_user_name').select2();
    $('#per_folder_name ').select2();
  });

  $(document).on("click", ".status-save-btn", function(e) {
    e.preventDefault();
    var $this = $(this);
    $.ajax({
      url: "{{route('postman.status.create')}}",
      type: "post",
      data: $('#status-create-form').serialize()
    }).done(function(response) {
      if (response.code = '200') {
        $('#loading-image').hide();
        $('#addPostman').modal('hide');
        toastr['success']('Status  Created successfully!!!', 'success');
        location.reload();
      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      toastr['error'](errObj.message, 'error');
    });
  });


  $(document).ready(function() {
    $('.status-dropdown').change(function(e) {
      e.preventDefault();
      var postId = $(this).data('id');
      var selectedStatus = $(this).val();
      console.log("Dropdown data-id:", postId);
      console.log("Selected status:", selectedStatus);


      // Make an AJAX request to update the status
      $.ajax({
        url: '/postman/update-status',
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          postId: postId,
          selectedStatus: selectedStatus
        },
        success: function(response) {
          toastr['success']('Status  Created successfully!!!', 'success');
          console.log(response);
        },
        error: function(xhr, status, error) {
          // Handle the error here
          console.error(error);
        }
      });
    });

    $('.api-issue-fix-done-dropdown').change(function(e) {
      e.preventDefault();
      var postId = $(this).data('id');
      var selectedValue = $(this).val();

      // Make an AJAX request to update the status
      $.ajax({
        url: '/postman/update-api-issue-fix-done',
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          postId: postId,
          selectedValue: selectedValue
        },
        success: function(response) {
          toastr['success'](response.message, 'success');
        },
        error: function(xhr, status, error) {
          // Handle the error here
          console.error(error);
        }
      });
    });
  });

    // Load settings value Histories
    $(document).on('click', '.status-history-show', function() {
            var id = $(this).attr('data-id');
                $.ajax({
                    method: "GET",
                    url: `{{ route('postman.status.histories', [""]) }}/` + id,
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            var html = "";
                            $.each(response.data, function(k, v) {
                                html += `<tr>
                                            <td> ${k + 1} </td>
                                            <td> ${(v.old_value != null) ? v.old_value.status_name : ' - ' } </td>
                                            <td> ${(v.new_value != null) ? v.new_value.status_name : ' - ' } </td>
                                            <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                            <td> ${v.created_at} </td>
                                        </tr>`;
                            });
                            $("#postman-status-histories-list").find(".postman-status-histories-list-view").html(html);
                            $("#postman-status-histories-list").modal("show");
                        } else {
                            toastr["error"](response.error, "Message");
                        }
                    }
                });
      });

      // Load settings value Histories
    $(document).on('click', '.api-issue-fix-done-history-show', function() {
      var id = $(this).attr('data-id');
      $.ajax({
          method: "GET",
          url: `{{ route('postman.api-issue-fix-done.histories', [""]) }}/` + id,
          dataType: "json",
          success: function(response) {
              if (response.status) {
                  var html = "";
                  $.each(response.data, function(k, v) {
                      html += `<tr>
                                  <td> ${k + 1} </td>
                                  <td> ${v.old_value_text} </td>
                                  <td> ${v.new_value_text} </td>
                                  <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                  <td> ${v.created_at} </td>
                              </tr>`;
                  });
                  $("#postman-api-issue-fix-done-histories-list").find(".postman-api-issue-fix-done-histories-list-view").html(html);
                  $("#postman-api-issue-fix-done-histories-list").modal("show");
              } else {
                  toastr["error"](response.error, "Message");
              }
          }
      });
    });

    $(document).on('click', '.create-quick-task', function() {
        var $this = $(this);
        site = $(this).data("id");
        title = $(this).data("title");
        cat_title = $(this).data("category_title");
        development = $(this).data("development");
        if (!title || title == '') {
            toastr["error"]("Please add title first");
            return;
        }
        //debugger;
        //let val = $("#change_website1").select2("val");

        let val = [29];
        $.ajax({
            url: '/task/get/websitelist',
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: val,
                cat_title:cat_title
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            //$this.siblings('input').val("");
            $('.website-list').html(response.data);
            //toastr["success"]("Remarks fetched successfully");
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
        });

        $("#create-quick-task").modal("show");

        var selValue = $(".save-item-select").val();
        if (selValue != "") {
            $("#create-quick-task").find(".assign-to option[value=" + selValue + "]").attr('selected',
                'selected')
            $('.assign-to.select2').select2({
                width: "100%"
            });
        }

        $("#hidden-task-subject").val(title);
        $(".text-task-development").val(development);
        $('#site_id').val(site);
    });

    $(document).on("click", ".create-task", function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        $.ajax({
            url: form.attr("action"),
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: form.serialize(),
            beforeSend: function() {
                $(this).text('Loading...');
            },
            success: function(response) {
                if (response.code == 200) {
                    form[0].reset();
                    toastr['success'](response.message);
                    $("#create-quick-task").modal("hide");
                } else {
                    toastr['error'](response.message);
                }
            }
        }).fail(function(response) {
            toastr['error'](response.responseJSON.message);
        });
    });

    $(document).on("click", ".count-dev-customer-tasks", function() {

        var $this = $(this);
        // var user_id = $(this).closest("tr").find(".ucfuid").val();
        var site_id = $(this).data("id");
        var category_id = $(this).data("category");
        $("#site-development-category-id").val(category_id);
        $.ajax({
            type: 'get',
            url: 'postman/countdevtask/' + site_id,
            dataType: "json",
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(data) {
                $("#dev_task_statistics").modal("show");
                var table = `<div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th width="4%">Tsk Typ</th>
                            <th width="4%">Tsk Id</th>
                            <th width="7%">Asg to</th>
                            <th width="12%">Desc</th>
                            <th width="12%">Sts</th>
                            <th width="33%">Communicate</th>
                            <th width="10%">Action</th>
                        </tr>`;
                for (var i = 0; i < data.taskStatistics.length; i++) {
                    var str = data.taskStatistics[i].subject;
                    var res = str.substr(0, 100);
                    var status = data.taskStatistics[i].status;
                    if (typeof status == 'undefined' || typeof status == '' || typeof status ==
                        '0') {
                        status = 'In progress'
                    };
                    table = table + '<tr><td>' + data.taskStatistics[i].task_type + '</td><td>#' +
                        data.taskStatistics[i].id +
                        '</td><td class="expand-row-msg" data-name="asgTo" data-id="' + data
                        .taskStatistics[i].id + '"><span class="show-short-asgTo-' + data
                        .taskStatistics[i].id + '">' + data.taskStatistics[i].assigned_to_name
                        .replace(/(.{6})..+/, "$1..") +
                        '</span><span style="word-break:break-all;" class="show-full-asgTo-' + data
                        .taskStatistics[i].id + ' hidden">' + data.taskStatistics[i]
                        .assigned_to_name +
                        '</span></td><td class="expand-row-msg" data-name="res" data-id="' + data
                        .taskStatistics[i].id + '"><span class="show-short-res-' + data
                        .taskStatistics[i].id + '">' + res.replace(/(.{7})..+/, "$1..") +
                        '</span><span style="word-break:break-all;" class="show-full-res-' + data
                        .taskStatistics[i].id + ' hidden">' + res + '</span></td><td>' + status +
                        '</td><td><div class="col-md-10 pl-0 pr-1"><textarea rows="1" style="width: 100%; float: left;" class="form-control quick-message-field input-sm" name="message" placeholder="Message"></textarea></div><div class="p-0"><button class="btn btn-sm btn-xs send-message" title="Send message" data-taskid="' +
                        data.taskStatistics[i].id +
                        '"><i class="fa fa-paper-plane"></i></button></div></td><td><button type="button" class="btn btn-xs load-communication-modal load-body-class" data-object="' +
                        data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                        .id +
                        '" title="Load messages" data-dismiss="modal"><i class="fa fa-comments"></i></button>';
                    table = table + '<a href="javascript:void(0);" data-task-type="' + data
                        .taskStatistics[i].task_type + '" data-id="' + data.taskStatistics[i].id +
                        '" class="delete-dev-task-btn btn btn-xs"><i class="fa fa-trash"></i></a>';
                    table = table +
                        '<button type="button" class="btn btn-xs  preview-img pd-5" data-object="' +
                        data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                        .id + '" data-dismiss="modal"><i class="fa fa-list"></i></button></td>';
                    table = table + '</tr>';
                }
                table = table + '</table></div>';
                $("#loading-image").hide();
                $(".modal").css("overflow-x", "hidden");
                $(".modal").css("overflow-y", "auto");
                $("#dev_task_statistics_content").html(table);
            },
            error: function(error) {
                console.log(error);
                $("#loading-image").hide();
            }
        });


    });
</script>
@endsection