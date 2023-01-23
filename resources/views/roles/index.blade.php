@extends('layouts.app')

@section('styles')

    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Role Management (<span id="roles_count">{{ $roles->total() }}</span>)</h2>
            <div class="pull-left">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group cls_filter_inputbox" style="width: 200px;">
                                <select name="term[]" class="form-control selectpicker" title="select roles" multiple id="term">
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="{{asset('/images/filter.png')}}"/></button>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="{{asset('/images/resend2.png')}}"/></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                @if(auth()->user()->checkPermission('roles-create'))
                    {{--                    <a class="btn btn-secondary" href="{{ route('roles.create') }}">+</a>--}}
                    <button class="btn btn-secondary" style="color:white;" data-toggle="modal" data-target="#newCreateRole"> +</button>
                @endif
            </div>
        </div>
    </div>


    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered" id="roles-table">
            <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th width="280px">Action</th>
            </tr>
            </thead>
            <tbody>
            @include('roles.partials.list-roles')
            </tbody>
        </table>
    </div>


    {!! $roles->render() !!}

    <div id="newCreateRole" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {!! Form::open(array('route' => 'roles.store','method'=>'POST')) !!}
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Role Management</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" style="text-align: -webkit-right;">
                            <div class="form-group cls_filter_inputbox" style="width: 200px;">
                                {!! Form::text('search_role', null, array('placeholder' => 'Search Permission','class' => 'form-control search_role')) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group permissions">
                                <strong>Permission:</strong>
                                <br/>
                                @foreach($permission as $value)
                                    <label>{{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name mt-3 h-auto')) }}
                                        <span> {{ $value->name }} </span></label>
                                    <br/>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary form-save-btn">Save changes</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script type="text/javascript">
        $('.select-multiple').select2({width: '100%'});

        function submitSearch(){
            src = '/roles'
            term = $('#term').val()
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    term : term,
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
                $("#roles-table tbody").empty().html(data.tbody);
                $("#roles_count").text(data.count);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }

            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });

        }

        function resetSearch(){
            src = '/roles'
            blank = ''
            $.ajax({
                url: src,
                dataType: "json",
                data: {

                    blank : blank,

                },
                beforeSend: function () {
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
                $('#term').val('')
                $('#user-select').val('')
                $("#roles-table tbody").empty().html(data.tbody);
                $("#roles_count").text(data.count);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }

            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        }

        $('.search_role').on('keyup',function(){
            var search_role = $('.search_role').val();
            var permission = $.map($(':checkbox[name=permission\\[\\]]:checked'), function(n, i){
                return n.value;
            }).join(',');

            $.ajax({
                url: "{{route('search_role')}}",
                dataType: "json",
                data: {search_role: search_role,permission:permission},
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done(function (data) {
                $("#loading-image").hide();
                $('.permissions').html(data);
            });
        });
    </script>

@endsection
