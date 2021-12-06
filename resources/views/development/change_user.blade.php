@extends('layouts.app')

@section('favicon' , 'development-issue.png')
@section('title', 'Change User')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style type="text/css">

    </style>
@endsection

<style> 
    .status-selection .btn-group {
        padding: 0;
        width: 100%;
    }
    .status-selection .multiselect {
        width : 100%;
    }
    .pd-sm {
        padding: 0px 8px !important;
    }
    tr {
        background-color: #f9f9f9;
    }
    .mr-t-5 {
        margin-top:5px !important;
    }
    /* START - Pupose : Set Loader image - DEVTASK-4359*/
    #myDiv{
        position: fixed;
        z-index: 99;
        text-align: center;
    }
    #myDiv img{
        position: fixed;
        top: 50%;
        left: 50%;
        right: 50%;
        bottom: 50%;
    }
    /* END - DEVTASK-4359*/
</style>


@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">            
            <h2 class="page-heading">{{ ucfirst($title) }} ({{$issues->total()}})</h2>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;">
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <form action="{{ url("development/change-user") }}" method="get" class="search">
                <div class="row">
                   
                    <div class="col-md-2 pd-sm">
                        <select class="form-control" name="user" id="user">
                            <option value="">Assigned To</option>
                            @foreach($users as $id=>$userData)
                                <option {{$user==$id ? 'selected' : ''}} value="{{$id}}">{{ $userData }}</option>
                            @endforeach
                        </select>
                     </div>
                    
               
                    <button type="submit" class="btn btn-image search">
                        <img src="{{ asset('images/search.png') }}" alt="Search">
                    </button>
                </div>
            </form>
             @if (auth()->user()->isAdmin())
             <a class="btn btn-secondary assignTask" style="color:white;" >Assign Task</a>
            @endif
            
           


         
        </div>
    </div>
    <div id="assigntask" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Assign Task</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form style="padding:10px;" action="{{ route('development.changeuser.store') }}" method="POST" id="changeuserForm">
                    @csrf
                    <div class="form-group">
                        <select class="form-control" name="change_user_id" id="change_user_id">
                            <option value="">Change  User</option>
                            @foreach($users as $id=>$userData)
                                <option  value="{{$id}}">{{ $userData }}</option>
                            @endforeach
                        </select>
    
                        @if ($errors->has('name'))
                            <div class="alert alert-danger">{{$errors->first('name')}}</div>
                        @endif
                    </div>
                    <input type="hidden" value=""/>
                    <input type="hidden" value="{{$user}}" name="assign_user_id" />
                    <button type="submit" class="btn btn-secondary ml-3">Change User</button>
                </form>
            </div>
        </div>
    </div>
   

    <?php
        $query = http_build_query(Request::except('page'));
        $query = url()->current() . (($query == '') ? $query . '?page=' : '?' . $query . '&page=');
    ?>

    
    <div class="infinite-scroll">
        <table class="table table-bordered table-striped" style="table-layout:fixed;">
            <tr>
                <th style="width:15%;">ID</th>
                <th style="width:8%;">Module</th>
                <th style="width:12%;">Subject</th>
                
            </tr>
            @foreach ($issues as $key => $issue)
            <tr>
            <td>
                {{$issue->id}}
                {{-- <a href="{{ url("development/task-detail/$issue->id") }}">{{ $issue->id }}
                    @if($issue->is_resolved==0)	
                        <input type="checkbox" name="selected_issue[]" value="{{$issue->id}}" {{in_array($issue->id, $priority) ? 'checked' : ''}}>	
                    @endif	
                </a> --}}
            </td>
            <td><a href="{{ url("development/task-detail/$issue->id") }}">{{ $issue->developerModule ? $issue->developerModule->name : 'Not Specified' }}</a></td>
        
            <td>{{ $issue->subject }}</td>
        </tr>
            @endforeach
        </table>
    </div>

@endsection
@section('scripts')

    <script src="{{env('APP_URL')}}/js/bootstrap-datetimepicker.min.js"></script>
    <script src="{{env('APP_URL')}}/js/jquery-ui.js"></script>
    <script src="{{env('APP_URL')}}/js/jquery.jscroll.min.js"></script>
    <script src="{{env('APP_URL')}}/js/bootstrap-multiselect.min.js"></script>
    <script src="{{env('APP_URL')}}/js/bootstrap-filestyle.min.js"></script>

  <script>
      $('.assign-user.select2').select2({
        width: "100%"
    });
    $(document).on('click', '.assignTask', function (event) {
        event.preventDefault();
        if($("#user").val()==""){
            alert("Please select Assigned User")
        }else{
            $("#assigntask").modal();
        }
            
        
    })

  </script>
@endsection
