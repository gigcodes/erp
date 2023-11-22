@extends('layouts.app')

@section('content')

<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
  </div>
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Sonar Cube</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-6">
                    <form method="get">
                        <div class="flex">
                            <div class="col" style="padding: 0;">
                                <?php echo Form::text("search", request("search", null), ["class" => "form-control", "placeholder" => "Enter input here.."]); ?>
                            </div>
                            <button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image">
                                <img src="/images/search.png" style="cursor: default;">
                            </button>
                            <a href="{{route('sonarqube.list.page')}}" type="button" class="btn btn-image" id=""><img src="/images/resend2.png"></a>    
                        </div>
                    </form>
                </div>
                <div class="col-6">
                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary" onclick="listuserprojects()"> Sonar project deatils </button>
                        <button type="button" class="btn btn-secondary" onclick="listprojects()">list the projects </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#sonar-project-create"> Create Project </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="project-list">
                        <tr>
                            <th width="5%">S.No</th>
                            <th width="10%">Severity</th>
                            <th width="10%">Component</th>
                            <th width="10%">project</th>
                            <th width="10%">Status</th>
                            <th width="10%">Message</th>
                            <th width="10%">Author</th>
                            <th width="5%">Create Date</th>
                            <th width="5%">close Date</th>
                            <th width="5%" style="overflow-wrap: anywhere;">Action</th>
                        </tr>
                        @foreach ($issues as $key=>$issue)
                            <tr>
                                <td>{{ $issue['id'] }}</td>
                                <td>{{ $issue['severity'] }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($issue['component']) > 30 ? substr($issue['component'], 0, 30).'...' :  $issue['component'] }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $issue['component'] }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($issue['project']) > 30 ? substr($issue['project'], 0, 30).'...' :  $issue['project'] }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $issue['project'] }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $issue['status'] }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($issue['message']) > 30 ? substr($issue['message'], 0, 30).'...' :  $issue['message'] }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $issue['message'] }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $issue['author'] }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ \Carbon\Carbon::parse($issue['creationDate'])->format('m-d F') }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    @if(isset($issue['closeDate']) && $issue['closeDate'])
                                        {{ \Carbon\Carbon::parse($issue['closeDate'])->format('m-d F') }}
                                    @else
                                        -
                                    @endif
                                </td>   
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$issue["id"]}}')"><i class="fa fa-arrow-down"></i></button>
                                </td>                             
                            </tr>

                            <tr class='action-btn-tr-{{$issue["id"]}} d-none'>
                                <td class="font-weight-bold">Action</td>
                                <td colspan="8" class="cls-actions">
                                    <div>
                                        <div class="row cls_action_box" style="margin:0px;">
                                            <button style="padding:3px;" title="create quick task" type="button" class="btn btn-image d-inline create-quick-task " data-id="@if ($issue) {{ $issue['id'] }} @endif"  data-category_title="Sonar Qube Page" data-title="@if ($issue) {{'Sonar Qube Page - '.$issue['id'] }} @endif"><i class="fa fa-plus" aria-hidden="true"></i></button>

                                            <button style="padding-left: 0;padding-left:3px;" type="button" class="btn btn-image d-inline count-dev-customer-tasks" title="Show task history" data-id="@if ($issue) {{ $issue['id'] }} @endif" data-category="{{ $issue['id'] }}"><i class="fa fa-info-circle"></i></button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    {{ $issues->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>


<div id="sonar-project-create" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="sonar-project-create-form" class="form mb-15" >
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Create Project</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="form-group">
                    <strong>Project display name:</strong>
                    {!! Form::text('project', null, ['placeholder' =>'Project display name', 'id' => 'project', 'class' => 'form-control', 'required' => 'required']) !!}
                </div>
                <div class="form-group">
                    <strong>Project key :</strong>
                    {!! Form::text('name', null, ['placeholder' => 'Project key', 'id' => 'name', 'class' => 'form-control', 'required' => 'required']) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-secondary">Add</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div id="sonar-project-list-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="modal-title"><b>Project Lists</b></h4>
                </div>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12" id="project-list-modal-html">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="sonar-user-project-list-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="modal-title"><b>Sonar User Project Lists</b></h4>
                </div>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12" id="project-user-list-modal-html">

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
            <form action="<?php echo route('task.create.multiple.task.shortcutsonar'); ?>" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Create Task</h4>
                </div>
                <div class="modal-body">

                    <input class="form-control" value="58" type="hidden" name="category_id" />
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
                    <!-- <div class="form-group">
                        <label for="">Websites</label>
                        <div class="form-group website-list row">
                           
                        </div>
                    </div> -->
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

<div id="preview-task-image" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered" style="table-layout: fixed">
                        <thead>
                            <tr>
                                <th style="width: 5%;">Sl no</th>
                                <th style=" width: 30%">Files</th>
                                <th style="word-break: break-all; width: 40%">Send to</th>
                                <th style="width: 10%">User</th>
                                <th style="width: 10%">Created at</th>
                                <th style="width: 15%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="task-image-list-view">
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

<script type="text/javascript">

function Showactionbtn(id){
    $(".action-btn-tr-"+id).toggleClass('d-none')
}

$(document).on('click', '.expand-row', function () {
    var selection = window.getSelection();
    if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
    }
});

$(document).on('submit', '#sonar-project-create-form', function(e){
    e.preventDefault();
    var self = $(this);
    let formData = new FormData(document.getElementById('sonar-project-create-form'));
    var button = $(this).find('[type="submit"]');
    $.ajax({
        url: '{{ route("sonarqube.createProject") }}',
        type: "POST",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        beforeSend: function() {
            button.html(spinner_html);
            button.prop('disabled', true);
            button.addClass('disabled');
        },
        complete: function() {
            button.html('Add');
            button.prop('disabled', false);
            button.removeClass('disabled');
        },
         }).done(function(response) {
            if(response.code == 200)
            {
                toastr["success"](response.message);
                  location.reload();
            } else {
                toastr["error"](response.message);
            }
    }).fail(function(response) {
        toastr["error"]("something went wrong");
    });
   
});

function listprojects() {
    $.ajax({
        url: '{{ route('sonarqube.list.Project') }}',
        dataType: "json",
    }).done(function(response) {
        $('.ajax-loader').hide();
        $('#project-list-modal-html').empty().html(response.html);
        $('#sonar-project-list-modal').modal('show');
        renderdomainPagination(response.data);
    }).fail(function(response) {
        $('.ajax-loader').hide();
        console.log(response);
    });
}

function listuserprojects() {
    $.ajax({
        url: '{{ route('sonarqube.user.projects') }}',
        dataType: "json",
    }).done(function(response) {
        $('.ajax-loader').hide();
        $('#project-user-list-modal-html').empty().html(response.html);
        $('#sonar-user-project-list-modal').modal('show');
        renderdomainPagination(response.data);
    }).fail(function(response) {
        $('.ajax-loader').hide();
        console.log(response);
    });
}

</script>

@endsection