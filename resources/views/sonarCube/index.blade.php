@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Sonar Cube ({{$issues->total()}})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-12">
                    <form method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <?php echo Form::text("search", request("search", null), ["class" => "form-control", "placeholder" => "Enter input here.."]); ?>
                            </div>
                            <div class="col-md-2">
                                <label for="severity">Severity </label>
                                <select class="form-control select2", multiple name="severity[]" id="severity">
                                    @foreach($issuesFilterSeverity as $k=>$v)
                                        <option value="{{$k}}"
                                        @if(is_array(request('severity')) && in_array($k, request('severity')))
                                            selected	
                                        @endif
                                        >{{$v}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="author">Author </label>
                                <select class="form-control select2", multiple name="author[]" id="author">
                                    @foreach($issuesFilterAuthor as $k=>$v)
                                        <option value="{{$k}}"
                                        @if(is_array(request('author')) && in_array($k, request('author')))
                                            selected	
                                        @endif
                                        >{{$v}}</option>
                                    @endforeach                                   
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="project">Project </label>
                                <select class="form-control select2", multiple name="project[]" id="project">
                                    @foreach($issuesFilterProject as $k=>$v)
                                        <option value="{{$k}}"
                                        @if(is_array(request('project')) && in_array($k, request('project')))
                                            selected	
                                        @endif
                                        >{{$v}}</option>
                                    @endforeach   
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image">
                                    <img src="/images/search.png" style="cursor: default;">
                                </button>
                                <a href="{{route('sonarqube.list.page')}}" type="button" class="btn btn-image" id=""><img src="/images/resend2.png"></a>    
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-12">
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>

<script type="text/javascript">
    
$('select.select2').select2();

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