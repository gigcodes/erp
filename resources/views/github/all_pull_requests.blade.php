@extends('layouts.app')

@section('content')
<style>
    #pull-request-table_filter {
        text-align: right;
    }
	table{
        margin: 0 auto;
        width: 100%;
        clear: both;
        border-collapse: collapse;
        table-layout: fixed; // ***********add this
        word-wrap:break-word; // ***********and this
    }
    .d-n{
        display: none;
    }
</style>

@include('github.repo_details')

<div class="row">
    <div class="col-lg-12 margin-tb page-heading">
        <h2 class="page-heading">Pull Requests (<span id="pull_request_html_id"></span>)</h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 margin-tb page-heading">
        @if(session()->has('message'))
            @php $type = Session::get('alert-type', 'info'); @endphp
            @if($type == "info")
                <div class="alert alert-secondary">
                    {{ session()->get('message') }}
                </div>
            @elseif($type == "warning")
                <div class="alert alert-warning">
                    {{ session()->get('message') }}
                </div>
            @elseif($type == "success")
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>    
            @elseif($type == "error")
                <div class="alert alert-error">
                    {{ session()->get('message') }}
                </div>    
            @endif
        @endif
    </div>
</div>

<div class="container" style="max-width: 100%;width: 100%;">
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="" class="form-label">Organization</label>
            <select name="organizationId" id="organizationId" class="form-control">
                @foreach ($githubOrganizations as $githubOrganization)
                    <option value="{{ $githubOrganization->id }}" data-repos='{{ $githubOrganization->repos }}' {{ ($githubOrganization->name == 'MMMagento' ? 'selected' : '' ) }}>{{  $githubOrganization->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="" class="form-label">Repository</label>
            <select name="repoId" id="repoId" class="form-control">
                
            </select>
        </div>

        <div class="col-md-6">
            <div class="text-right pl-5">
                <button class="btn btn-sm btn-secondary" onclick="updateLabelActivities()"> Update Label Activities </button>
                <a class="btn btn-sm btn-secondary" id="repo_select">Select Repository</a>
                <a class="btn btn-sm btn-secondary" href="/github/repos/231925646/deploy?branch=master&pull_only=1">Deploy ERP Master</a>
                <a class="btn btn-sm btn-secondary" href="/github/repos/231925646/deploy?branch=master&composer=true&pull_only=1">Deploy ERP Master + Composer</a>
            </div>
        </div>
    </div>

    <table id="pull-request-table" class="table table-bordered" style="table-layout: fixed;">
        <thead>
            <tr>
                <th style="width:3% !important;"></th>
                <th style="width:7% !important;">Repository</th>
                <th style="width:7% !important;">Number</th>
                <th style="width:13% !important;">Title</th>
                <th style="width:10% !important;">Branch</th>
                <th style="width:10% !important;">User</th>
                <th style="width:10% !important;">Updated At</th>
                <th style="width:13% !important;">Latest Activity</th>
                <th style="width:10% !important;">Deploy</th>
                <th style="width:10% !important;">Actions</th>
            </tr>
        </thead>
        <tbody>
          
        </tbody>
    </table>
    <div class="loader-section d-n">
        <div style="position: relative;left: 0px;top: 0px;width: 100%;height: 120px;z-index: 9999;background: url({{ url('images/pre-loader.gif')}}) 50% 50% no-repeat;"></div>
    </div>
</div>
<!-- Modal markup -->
<div class="modal" id="pr-review-comments-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="pr-review-comments-modal-content">
            <!-- AJAX content will be loaded here -->
        </div>
    </div>
</div>
<!-- Modal markup -->
<div class="modal" id="pr-activities-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="pr-activities-modal-content">
            <!-- AJAX content will be loaded here -->
        </div>
    </div>
</div>
<!-- Modal markup -->
<div class="modal" id="pr-error-logs-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="pr-error-logs-modal-content">
            <!-- AJAX content will be loaded here -->
        </div>
    </div>
</div>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script>
    $('#pull-request-table').DataTable({
        "paging": false,
        "ordering": true,
        "info": false
    });

    function confirmMergeToMaster(branchName, url) {
        let result = confirm("Are you sure you want to merge " + branchName + " to master?");
        if (result) {
            window.location.href = url;
        }
    }

    function confirmClosePR(repositoryId, pullRequestNumber) {
        let result = confirm("Are you sure you want to close this PR : "+ pullRequestNumber+ "?");
        if (result) {
            $.ajax({
                headers : {
                    'Accept' : 'application/json',
                    'Content-Type' : 'application/json',
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: '/github/repos/'+repositoryId+'/pull-request/'+pullRequestNumber+'/close',
                dataType: "json",
                success: function (response) {
                    if(response.status) {
                        toastr['success']('Pull request has been closed successfully!');
                        window.location.reload();
                    }else{
                        errorMessage = response.error ? response.error : 'Something went wrong please try again later!';
                        toastr['error'](errorMessage);
                    }
                },
                error: function () {
                    toastr['error']('Could not change module!');
                }
            });
        }
    }

    $('#organizationId').change(function (){
        getRepositories();
    });

    function getRepositories(){
        var repos = $.parseJSON($('#organizationId option:selected').attr('data-repos'));

        $('#repoId').empty();

        if(repos.length > 0){
            $.each(repos, function (k, v){
                $('#repoId').append('<option value="'+v.id+'">'+v.name+'</option>');
            });

            getPullRequests();
        }else{
            getPullRequests();
        }
    }

    $('#repoId').change(function (){
        getPullRequests();
    });
    var xhr = null;
    function getPullRequests(){
        var repoId = $('#repoId').val();

        $('.loader-section').removeClass('d-n');
        
        xhr = $.ajax({
            type: "GET",
            url: "",
            async:true,
            data: {
                repoId: repoId,
            },
            beforeSend: function () {
                console.log("xhr-"+xhr);
                if (xhr != undefined && xhr !== null) {
                    xhr.abort();
                }
            },
            dataType: "json",
            success: function (result) {
                $('#pull-request-table').DataTable().clear().destroy();

                $('#pull_request_html_id').html(result.count);;
                $('#pull-request-table tbody').empty().html(result.tbody);

                $('#pull-request-table').DataTable({
                    "paging": false,
                    "ordering": true,
                    "info": false
                });

                $('.loader-section').addClass('d-n');
            }
        });
    }

    $(document).ready(function() {
        getRepositories();
    });

    $(document).ready(function() {
        var currentPage = 1;
        var currentPageActivity = 1;
        var currentPageErrorLogs = 1;

        $(document).on('click', '.show-pr-review-comments', function(e) {
            e.preventDefault();
            var repo = $(this).data("repo");
            var pullNumber = $(this).data("pull-number");

            // Make the AJAX request
            loadComments(currentPage, repo, pullNumber);
        });

        // Load comments for the given page number
        function loadComments(page, repo, pullNumber) {
            $('.loader-section').removeClass('d-n');
            $.ajax({
                url: "{{ url('/github/pull-request-review-comments') }}/" + repo + "/" + pullNumber + "?page=" + page,
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    $('.loader-section').addClass('d-n');
                    // Update the modal content with the retrieved comments
                    $('#pr-review-comments-modal-content').html(response);
                    $('#repo').val(repo);
                    $('#pullNumber').val(pullNumber);

                    // Show the modal
                    $('#pr-review-comments-modal').modal('show');
                },
                error: function(xhr, status, error) {
                    $('.loader-section').addClass('d-n');
                    // Handle the error, if any
                    console.error(error);
                }
            });
        }

        // Pagination click event
        $(document).on('click', '#pr-review-comments-modal .pagination a', function(e) {
            e.preventDefault();

            // Get the page number from the clicked link
            var page = $(this).attr('href').split('page=')[1];
            var repo = $("#pr-review-comments-modal #repo").val();
            var pullNumber = $("#pr-review-comments-modal #pullNumber").val();
            // Update the current page and load comments for the new page
            currentPage = page;
            loadComments(currentPage, repo, pullNumber);
        });

        $(document).on('click', '.show-pr-activities', function(e) {
            e.preventDefault();
            var repo = $(this).data("repo");
            var pullNumber = $(this).data("pull-number");

            // Make the AJAX request
            loadActivities(currentPageActivity, repo, pullNumber);
        });

        // Load activities for the given page number
        function loadActivities(page, repo, pullNumber) {
            $('.loader-section').removeClass('d-n');
            $.ajax({
                url: "{{ url('/github/pull-request-activities') }}/" + repo + "/" + pullNumber + "?page=" + page,
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    $('.loader-section').addClass('d-n');
                    // Update the modal content with the retrieved comments
                    $('#pr-activities-modal-content').html(response);
                    $('#pr-activities-modal #repo').val(repo);
                    $('#pr-activities-modal #pullNumber').val(pullNumber);

                    // Show the modal
                    $('#pr-activities-modal').modal('show');
                },
                error: function(xhr, status, error) {
                    $('.loader-section').addClass('d-n');
                    // Handle the error, if any
                    console.error(error);
                }
            });
        }

        // Pagination click event
        $(document).on('click', '#pr-activities-modal .pagination a', function(e) {
            e.preventDefault();

            // Get the page number from the clicked link
            var page = $(this).attr('href').split('page=')[1];
            var repo = $("#pr-activities-modal #repo").val();
            var pullNumber = $("#pr-activities-modal #pullNumber").val();
            // Update the current page and load comments for the new page
            currentPage = page;
            loadActivities(currentPage, repo, pullNumber);
        });
        
        // 
        $(document).on('click', '.show-pr-error-logs', function(e) {
            e.preventDefault();
            var repo_id = $(this).attr('data-repo');
            var pull_number = $(this).attr('data-pull-number');

            // Make the AJAX request
            loadErrorLogs(currentPageErrorLogs, repo_id, pull_number);
        });

        // Load activities for the given page number
        function loadErrorLogs(page, repo, pullNumber) {
            $('.loader-section').removeClass('d-n');
            $.ajax({
                url: "{{ url('/github/pr-error-logs') }}/" + repo + "/" + pullNumber + "?page=" + page,
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    $('.loader-section').addClass('d-n');
                    // Update the modal content with the retrieved comments
                    $('#pr-error-logs-modal-content').html(response);
                    $('#pr-error-logs-modal #repo').val(repo);
                    $('#pr-error-logs-modal #pullNumber').val(pullNumber);

                    // Show the modal
                    $('#pr-error-logs-modal').modal('show');
                },
                error: function(xhr, status, error) {
                    $('.loader-section').addClass('d-n');
                    // Handle the error, if any
                    console.error(error);
                }
            });
        }

        // Pagination click event
        $(document).on('click', '#pr-error-logs-modal .pagination a', function(e) {
            e.preventDefault();

            // Get the page number from the clicked link
            var page = $(this).attr('href').split('page=')[1];
            var repo = $("#pr-error-logs-modal #repo").val();
            var pullNumber = $("#pr-error-logs-modal #pullNumber").val();
            // Update the current page and load comments for the new page
            currentPage = page;
            loadErrorLogs(currentPage, repo, pullNumber);
        });

        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });
    });

    $(document).on('click', '#repo_select', function(event) {
       $('#create-repo-modal').modal('show');

    });
    
    $(document).ready(function() {
        $('.repostatus').on('change', function() {
            $('.repostatus').not(this).prop('checked', false);

            var selectedRepos = [];
            $('input[name="repostatus"]:checked').each(function() {
                selectedRepos.push($(this).val());
            });
            var repoId = $(this).data('repo_id');
            $.ajax({
                type: "GET",
                url: "{{route('github.repoStatusCheck')}}",
                data: {
                    _token: "{{ csrf_token() }}",
                    repoId: repoId
                },
                success: function(response) {
                    $('.ajax-loader').hide();
                    if (response.message) {
                        toastr['success'](response.message, 'Success');
                    } else {
                        toastr['error'](response.message, 'Error');
                    }
                },
                error: function(response) {
                    $('.ajax-loader').hide();
                    console.log(response);
                }
            });
        });
    });

    function updateLabelActivities()
    {
        event.preventDefault();
        var selected_rows = [];
        var selected_repo_id = "";

        $(".bulk_select_pull_request").each(function () {
            if ($(this).prop("checked") == true) {
                selected_rows.push($(this).val());
                selected_repo_id = $(this).data("repo");
            }
        });

        if (selected_rows.length == 0) {
            alert('Please select any row');
            return false;
        }

        if(confirm('Are you sure you want to perform this action?')==false)
        {
            console.log(selected_rows, selected_repo_id);
            return false;
        }

        $.ajax({
            type: "post",
            url: "{{ route('github.pull-request-activities.update') }}",
            data: {
                _token: "{{ csrf_token() }}",
                prIds: selected_rows,
                repoId: selected_repo_id
            },
            beforeSend: function() {
                $(this).attr('disabled', true);
                $('.loader-section').removeClass('d-n');
            }
        }).done(function(data) {
            $('.loader-section').addClass('d-n');
            toastr["success"]("Updated successfully!", "Message")
            window.location.reload();
        }).fail(function(response) {
            $('.loader-section').addClass('d-n');
            toastr["error"](error.responseJSON.message);
        });
    }
</script>
@endsection