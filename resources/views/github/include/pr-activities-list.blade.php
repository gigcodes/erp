@extends('layouts.app')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Pr Activites({{ $prActivities->total() }})</h2>
        </div>
    </div>
    <div class="col-md-12">
        <form action="{{ url('/github/new-pr-activities') }}" method="get" class="search">
            <div class="col-lg-2">
                <label> Search Organizations </label>
                <?php 
                if(request('org')){   $org_search = request('org'); }
                else{ $org_search = []; }
                ?>
                <select name="org[]" id="org" class="form-control select2" multiple>
                    <option value="" @if($org_search=='') selected @endif>-- Select a Organizations --</option>
                    @forelse($orgs as $id=>$org)
                    <option value="{{ $id }}" @if(in_array($id, $org_search)) selected @endif>{!! $org !!}</option>
                    @empty
                    @endforelse
                </select>
            </div>
            <div class="col-lg-2">
                <label> Search Pull Number</label>
                <input class="form-control" type="text" id="pull_num" placeholder="Search Pull Number" name="pull_num"
                value="{{ request('pull_num') ?? '' }}">
            </div>
            <div class="col-lg-2">
                <label> Search PR Title</label>
                <input class="form-control" type="text" id="pr_title" placeholder="Search Test" name="pr_title"
                    value="{{ request('pr_title') ?? '' }}">
            </div>

            <div class="col-lg-2">
                <label> Search Event</label>
                <input class="form-control" type="text" id="event" placeholder="Search Event" name="event"
                    value="{{ request('event') ?? '' }}">
            </div>
            <div class="col-lg-2">
                <label> Search Event header</label>
                <input class="form-control" type="text" id="event_header" placeholder="Search Event Header" name="event_header"
                    value="{{ request('event_header') ?? '' }}">
            </div>
            <div class="col-lg-2">
                <label> Search Label Name</label>
                <input class="form-control" type="text" id="label_name" placeholder="Search Label Name" name="label_name"
                    value="{{ request('label_name') ?? '' }}">
            </div>
            <div class="col-lg-2">
                <label> Search Repository </label>
                <?php 
                    if(request('repo')){   $repo_search = request('repo'); }
                    else{ $repo_search = []; }
                ?>
                <select name="repo[]" id="repo" class="form-control select2" multiple>
                    <option value="" @if($repo_search=='') selected @endif>-- Select a Repository --</option>
                    @forelse($repos as $id=>$repo)
                    <option value="{{ $id}}" @if(in_array($id, $repo_search)) selected @endif>{!! $repo!!}</option>
                    @empty
                    @endforelse
                </select>
            </div>
            <div class="col-lg-2">
                <label> Search User </label>
                <?php 
                    if(request('user')){   $user_search = request('user'); }
                    else{ $user_search = []; }
                ?>
                <select name="user[]" id="user" class="form-control select2" multiple>
                    <option value="" @if($user_search=='') selected @endif>-- Select a User --</option>
                    @forelse($users as $id=>$user)
                    <option value="{{ $user}}" @if(in_array($user, $user_search)) selected @endif>{!! $user!!}</option>
                    @empty
                    @endforelse
                </select>
            </div>
            <div class="col-lg-2">
                <label> Search Created At</label>
                <input class="form-control" type="date" name="date" value="{{ request('date') ?? '' }}">
            </div>

            <div class="col-lg-2">
                <button type="submit" class="btn btn-image search"
                    onclick="document.getElementById('download').value = 1;">
                    <img src="{{ asset('images/search.png') }}" alt="Search">
                </button>
                <a href="{{ url('/github/new-pr-activities') }}" class="btn btn-image" id=""><img
                        src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            </div>
        </form>
    </div>
    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pull Number</th>
                    <th>Repo organization</th>
                    <th>Repo Name</th>
                    <th>Event</th>
                    <th>Event Header</th>
                    <th>Body</th>
                    <th>Description</th>
                    <th>Lablel Name</th>
                    <th>user</th>
                    <th>Activity Created At</th>
                    <th>Created At</th>
                </tr>
            <tbody>
                @foreach ($prActivities as $prAct)
                    <tr>
                        <td>{{ $prAct->id }}</td>
                        <td>{{ $prAct->pull_number }}</td>
                        <td>{{ $prAct->githubOrganization?->name }}</td>
                        <td>{{ $prAct->githubRepository?->name }}</td>
                        <td>{{ $prAct->event }}</td>
                        <td>{{ $prAct->event_header }}</td>
                        <td style="cursor:pointer;" id="reply_text" class="expand-row error-text-modal"
                            data-id="{{ $prAct->id }}" data-message="{{ $prAct->body }}">
                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                <div class="td-mini-container">
                                    {!! strlen($prAct->body) > 20
                                        ? substr($prAct->body, 0, 45) . '...'
                                        : $prAct->body !!}
                                </div>
                                <div class="td-full-container hidden">
                                    {{ $prAct->body }}
                                </div>
                            </div>
                        </td>
                        <td style="cursor:pointer;" id="reply_text" class="expand-row error-text-modal"
                            data-id="{{ $prAct->id }}" data-message="{{ $prAct->description }}">
                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                <div class="td-mini-container">
                                    {!! strlen($prAct->description) > 20
                                        ? substr($prAct->description, 0, 45) . '...'
                                        : $prAct->description !!}
                                </div>
                                <div class="td-full-container hidden">
                                    {{ $prAct->description }}
                                </div>
                            </div>
                        </td>
                        <td>{{ $prAct->label_name }}</td>
                        <td>{{ $prAct->user }}</td>
                        <td>{{ \Carbon\Carbon::parse($prAct->activity_created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>{{ $prAct->created_at?->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
            </thead>
        </table>
        {!! $prActivities->appends(Request::except('page'))->links() !!}
    </div>
    <div id="loading-image"
        style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
    </div>
    <div class="modal fade" id="magento-error-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Magento problem</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <textarea id="magento-error-body-text" class="form-control" name="reply"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        $('.select2').select2();

        $(document).on("click", ".error-text-modal", function(e) {
            e.preventDefault();
            var $this = $(this);
            $("#magento-error-body-text").val($this.data("message"));
            $("#magento-error-modal").modal("show");
        });
    </script>
@endsection