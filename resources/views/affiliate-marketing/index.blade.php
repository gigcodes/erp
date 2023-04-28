@extends('layouts.app')
@section('title', 'Affiliate Marketing')
@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
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
                Affiliates providers (<span id="affiliate_count">{{ $providers->total() }}</span>)
            </h2>
            <div class="pull-left">
                <form action="{{route('affiliate-marketing.providers')}}">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <input name="provider_name" type="text" class="form-control"
                                       value="{{ request('provider_name') }}" placeholder="Search providers">
                            </div>
                            <div class="col-md-4">
                                {!! Form::select("status", ["" => "Select status", "active" => "Active", "inactive" => "Inactive"], request('status'), ["class" => "form-control type-filter"]) !!}
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-image">
                                    <img src="/images/filter.png"/>
                                </button>
                                <button type="reset"
                                        onclick="window.location='{{route('affiliate-marketing.providers')}}'"
                                        class="btn btn-image" id="resetFilter">
                                    <img src="/images/resend2.png"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-2 pl-0 float-right">
                <button data-toggle="modal" data-target="#create-provider" type="button"
                        class="float-right mb-3 btn-secondary">New Provider
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
                <th>Name</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($providers as $key => $affiliate)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $affiliate->provider_name }}</td>
                    <td>{{ $affiliate->status ? 'Active': 'Inactive' }}</td>
                    <td>
                        <button type="button" data-toggle="modal" data-target="#create-provider"
                                onclick="editData('{!! $affiliate->id !!}')"
                                class="btn btn-image"><img src="/images/edit.png"></button>
                        {!! Form::open(['method' => 'POST','route' => ['affiliate-marketing.deleteProviders'],'style'=>'display:inline']) !!}
                        <input type="hidden" value="{{$affiliate->id}}" name="id">
                        <button type="submit" class="btn btn-image"><img src="/images/delete.png"/></button>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $providers->render() !!}

    <div class="modal fade" id="create-provider" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Create Affiliate Provider</h2>
                    </div>
                    <form id="create-provider-form" method="POST"
                          action="{{route('affiliate-marketing.createProvider')}}">
                        {{csrf_field()}}
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Provider name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="provider_name" name="provider_name"
                                       placeholder="Provider name" value="{{ old('provider_name') }}">
                                @if ($errors->has('provider_name'))
                                    <span class="text-danger">{{$errors->first('provider_name')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-sm-2 col-form-label">Status</label>
                            <div class="col-sm-10">
                                <select class="browser-default custom-select" id="status" name="status"
                                        style="height: auto">
                                    <option value="true" selected>Active</option>
                                    <option value="false">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                                    aria-label="Close">Close
                            </button>
                            <button type="submit" class="float-right custom-button btn">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

    <script type="text/javascript">
        let showPopup;
        @if(Session::get('create_popup'))
            showPopup = true;
        @endif

        if (showPopup) {
            $('#create-provider').modal('show');
        }

        $('#create-provider').on('show.bs.modal', function () {
            $('#create-provider .page-header h2').text('Create Affiliate Provider');
            $('#create-provider-form').attr('action', "{{ route('affiliate-marketing.createProvider') }}");
            $('#create-provider-form button[type="submit"]').text('Create');
        })

        $('#create-provider').on('hidden.bs.modal', function () {
            $('#create-provider-form').get(0).reset();
        })

        function editData(id) {
            let url = "{{ route('affiliate-marketing.getProvider', [":id"]) }}";
            url = url.replace(':id', id);
            $.ajax({
                url,
                type: 'GET',
                params: {id},
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (response) {
                    debugger;
                    $("#loading-image").hide();
                    if (!response.status) {
                        toastr["error"](response.message);
                        $('#create-provider').modal('hide');
                    } else {
                       // $('#create-provider-form').attr('action', "{{ route('affiliate-marketing.createProvider') }}");
                        let url = "{{ route('affiliate-marketing.updateProvider', [":id"]) }}";
                        url = url.replace(':id', id);
                        $('#create-provider-form').attr('action', url);
                        $('#create-provider-form button[type="submit"]').text('Update');
                        $('#create-provider .page-header h2').text('Update Affiliate Provider');
                        $('#create-provider-form [name="provider_name"]').val(response.data.provider_name);
                        $('#create-provider-form [name="status"]').val(response.data.status ? 'true' : 'false');
                    }
                }
            })
        }
    </script>
@endsection
