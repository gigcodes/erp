@extends('layouts.app')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Magento Problems({{ $magentoProblems->total() }})</h2>
        </div>
    </div>
    <div class="mt-3 col-md-12">
        <form action="{{ route('magento-problems-lists') }}" method="get" class="search">
            <div class="col-lg-2">
                <label> Search Source</label>
                <input class="form-control" type="text" id="search_source" placeholder="Search Source" name="search_source"
                value="{{ request('search_source') ?? '' }}">
            </div>
            <div class="col-lg-2">
                <label> Search Test</label>
                <input class="form-control" type="text" id="search_test" placeholder="Search Test" name="search_test"
                    value="{{ request('search_test') ?? '' }}">
            </div>
            <div class="col-lg-2">
                <label> Search Serverity</label>
                <input class="form-control" type="text" id="search_severity" placeholder="Search Severity" name="search_severity"
                    value="{{ request('search_severity') ?? '' }}">
            </div>
            <div class="col-lg-2">
                <label> Search Type</label>
                <input class="form-control" type="text" id="type" placeholder="Search Type" name="type"
                    value="{{ request('type') ?? '' }}">
            </div>
            <div class="col-lg-2">
                <label> Search Error Body</label>
                <input class="form-control" type="text" id="error_body" placeholder="Search Error body" name="error_body"
                    value="{{ request('error_body') ?? '' }}">
            </div>
            <div class="col-lg-2 pd-sm">
                <label> Search Status</label>
                <select name="status" id="status" class="form-control globalSelect" data-placeholder="Select Status">
                    <option  Value="">Select status</option>
                    <option  Value="open" {{ (request('status') == "open") ? "selected" : "" }} >Open</option>
                    <option value="closed"{{ (request('status') == "closed") ? "selected" : "" }}>Closed</option>
                </select>
                </div>  
            <div class="col-lg-2"><br>
                <label> Search date</label>
                <input class="form-control" type="date" name="date" value="{{ request('date') ?? '' }}">
            </div>

            <div class="col-lg-2"><br><br>
                <button type="submit" class="btn btn-image search"
                    onclick="document.getElementById('download').value = 1;">
                    <img src="{{ asset('images/search.png') }}" alt="Search">
                </button>
                <a href="{{ route('magento-problems-lists') }}" class="btn btn-image" id=""><img
                        src="/images/resend2.png" style="cursor: nwse-resize;"></a>

                <button type="button" class="btn custom-button float-right mr-3" data-toggle="modal" data-target="#status-create">Add Status</button>
            </div>
        </form>
    </div>
    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Source</th>
                    <th>Test</th>
                    <th>Severity</th>
                    <th>Error Body</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            <tbody>
                @foreach ($magentoProblems as $magentoProblem)
                    <tr>
                        <td>{{ $magentoProblem->id }}</td>
                        <td>{{ $magentoProblem->source }}</td>
                        <td>{{ $magentoProblem->test }}</td>
                        <td>{{ $magentoProblem->severity }}</td>
                        <td style="cursor:pointer;" id="reply_text" class="expand-row error-text-modal"
                            data-id="{{ $magentoProblem->id }}" data-message="{{ $magentoProblem->error_body }}">
                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                <div class="td-mini-container">
                                    {!! strlen($magentoProblem->error_body) > 20
                                        ? substr($magentoProblem->error_body, 0, 20) . '...'
                                        : $magentoProblem->error_body !!}
                                </div>
                                <div class="td-full-container hidden">
                                    {{ $magentoProblem->github_api_url }}
                                </div>
                            </div>
                        </td>
                        <td>{{ $magentoProblem->type }}</td>
                        @if($magentoProblem->status == 1)
                        <td>open</td>
                        @else 
                        <td>closed</td>
                        @endif
                        <td>{{ $magentoProblem->created_at?->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
            </thead>
        </table>
        {!! $magentoProblems->appends(Request::except('page'))->links() !!}
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
                            <textarea id="magento-error-body-text" class="form-control" name="reply" style="position: relative; height: 100%;" rows="15"></textarea>
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

@section('scripts')
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        $(document).on("click", ".error-text-modal", function(e) {
            e.preventDefault();
            var $this = $(this);
            $("#magento-error-body-text").val($this.data("message"));
            $("#magento-error-modal").modal("show");
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
    </script>
@endsection
