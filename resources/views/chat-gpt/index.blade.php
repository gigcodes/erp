@extends('layouts.app')
@section('title', 'Chat GPT')
@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 9999;
        }

        .btn-secondary, .btn-secondary:focus, .btn-secondary:hover {
            background: #fff;
            color: #757575;
            border: 1px solid #ddd;
            height: 32px;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: 100;
            line-height: 10px;
        }
    </style>
@endsection
@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">
                Chat GPT
            </h2>
            <div class="pull-left">
                <form action="{{route('chatgpt.index')}}">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <input name="prompt" type="text" class="form-control"
                                       value="{{ request('prompt') }}" placeholder="Search prompt">
                            </div>
                            <div class="col-md-4">
                                <input name="response" type="text" class="form-control"
                                       value="{{ request('response') }}" placeholder="Search response">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-image">
                                    <img src="/images/filter.png"/>
                                </button>
                                <button type="reset"
                                        onclick="window.location='{{route('chatgpt.index')}}'"
                                        class="btn btn-image" id="resetFilter">
                                    <img src="/images/resend2.png"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-2 pl-0 float-right">
                <button data-toggle="modal" data-target="#create-request" type="button"
                        class="float-right mb-3 btn-secondary">New request
                </button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered" id="affiliates-table">
            <thead>
            <tr>
                <th>No</th>
                <th>Prompt</th>
                <th>Response</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($responses as $key => $response)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $response->prompt }}</td>
                    <td>{{ $response->response }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $responses->render() !!}

    <div class="modal fade" id="create-request" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Get ChatGPT Response</h2>
                    </div>
                    <div class="form-group row">
                        <label for="headline1" class="col-sm-2 col-form-label">Question</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="chatgpt_question"
                                   name="question"
                                   placeholder="Question"
                                   value="{{ old('question') }}">
                            @if ($errors->has('question'))
                                <span class="text-danger">{{$errors->first('question')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="headline1" class="col-sm-12 col-form-label">Response:-</label>
                        <div class="col-sm-12" id="chatgpt_response">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                                aria-label="Close">Close
                        </button>
                        <button type="button" class="float-right custom-button btn" onclick="getResponse()">Get
                            Response
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

    <script type="text/javascript">
        function getResponse() {
            let question = $('#chatgpt_question').val();
            $.ajax({
                url: "{{ route('chatgpt.response') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    question,
                    "_token": "{{ csrf_token() }}"
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (response) {
                    $("#loading-image").hide();
                    if (!response.status) {
                        toastr["error"](response.message);
                    } else {
                        $('#chatgpt_response').html(response.data);
                    }
                }
            })
        }
    </script>
@endsection
