@extends('layouts.app')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">New Pull Requests({{ $pullRequests->total() }})</h2>
        </div>
    </div>
    <div class="col-md-12">
        <form action="{{ url('/github/new-pullRequests') }}" method="get" class="search">
            <div class="col-lg-2">
                <label> Search Repos </label>
                <?php 
                    if(request('repo_names')){   $repo_search = request('repo_names'); }
                    else{ $repo_search = []; }
                ?>
                <select name="repo_names[]" id="repo_names" class="form-control select2" multiple>
                    <option value="" @if($repo_search=='') selected @endif>-- Select a Repo Names --</option>
                    @forelse($repo_names as $repo_name)
                    <option value="{{ $repo_name }}" @if(in_array($repo_name, $repo_search)) selected @endif>{!! $repo_name !!}</option>
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
                <label> Search State</label>
                <input class="form-control" type="text" id="state" placeholder="Search State" name="state"
                    value="{{ request('state') ?? '' }}">
            </div>
            <div class="col-lg-2">
                <label> Search Users </label>
                <?php 
                    if(request('user')){   $user_search = request('user'); }
                    else{ $user_search = []; }
                ?>
                <select name="user[]" id="user" class="form-control select2" multiple>
                    <option value="" @if($user_search=='') selected @endif>-- Select a User --</option>
                    @forelse($users as $user)
                    <option value="{{ $user }}" @if(in_array($user, $user_search)) selected @endif>{!! $user !!}</option>
                    @empty
                    @endforelse
                </select>
            </div>
            <div class="col-lg-2">
                <label> Search date</label>
                <input class="form-control" type="date" name="date" value="{{ request('date') ?? '' }}">
            </div>

            <div class="col-lg-">
                <button type="submit" class="btn btn-image search"
                    onclick="document.getElementById('download').value = 1;">
                    <img src="{{ asset('images/search.png') }}" alt="Search">
                </button>
                <a href="{{ url('/github/new-pullRequests') }}" class="btn btn-image" id=""><img
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
                    <th>Repo Name</th>
                    <th>Pr Title</th>
                    <th>Pr url</th>
                    <th>State</th>
                    <th>Created by</th>
                    <th>Created At</th>
                </tr>
            <tbody>
                @foreach ($pullRequests as $pullRequest)
                    <tr>
                        <td>{{ $pullRequest->id }}</td>
                        <td>{{ $pullRequest->pull_number }}</td>
                        <td>{{ $pullRequest->repo_name }}</td>
                        <td>{{ $pullRequest->pr_title }}</td>
                        <td style="cursor:pointer;" id="reply_text" class="expand-row error-text-modal"
                            data-id="{{ $pullRequest->id }}" data-message="{{ $pullRequest->pr_url }}">
                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                <div class="td-mini-container">
                                    {!! strlen($pullRequest->pr_url) > 20
                                        ? substr($pullRequest->pr_url, 0, 45) . '...'
                                        : $pullRequest->pr_url !!}
                                </div>
                                <div class="td-full-container hidden">
                                    {{ $pullRequest->pr_url }}
                                </div>
                            </div>
                        </td>
                        <td>{{ $pullRequest->state }}</td>
                        <td>{{ $pullRequest->created_by }}</td>
                        <td>{{ $pullRequest->created_at?->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
            </thead>
        </table>
        {!! $pullRequests->appends(Request::except('page'))->links() !!}
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