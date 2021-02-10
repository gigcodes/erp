@extends('layouts.app')
@section('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Mailing list</h2>
                    <div class="pull-left">
                    </div>
                    <button class="btn btn-primary float-right" type="button" class="btn btn-primary"
                        data-toggle="modal" data-target="#exampleModal">Create Mailing list
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Mailing list</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="">
                        <div class="form-group">
                            <select name="service_id" id="service_id" class="form-control">
                                <option value="">Select Service</option>
                                @foreach($services as $service)
                                    <option value="{{$service->id}}">{{$service->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="websites_id" id="websites_id" class="form-control">
                                <option value="">Select Website</option>
                                @foreach($websites as $website)
                                    <option value="{{$website->id}}">{{$website->title}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <input type="email" name="email" id="email" class="form-control email" placeholder="Email">
                        </div>

                        <div class="form-group">
                            <input type="text" name="name" class="form-control name" placeholder="Name">
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary save_list">Save changes</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="table-responsive mt-3">
        {{$list->links()}}
        <table class="table table-bordered" id="passwords-table">
            <thead>
            <tr>
                <!-- <th style="">ID</th> -->
                <th style="">Name</th>
                <th style="">Website</th>
                <th style="">Email</th>
                <!--th style="">Service</th-->
                <th style="">RemoteID</th>
                <th style="">Actions</th>
            </thead>
            <tbody>
            @foreach($list as $value)
                <tr>
                    <td>{{$value->name}}</td>
                    <td>@if($value->website) {{$value->website->title}} @endif</td>
                    <td>{{$value['email']}}</td>
                    <!--td><?php //$value->service->name?></td-->
                    <td>{{$value['remote_id']}}</td>
                    <td><a href="{{route('mailingList.single', $value['remote_id'])}}"><i class="fa fa-list"></i></a> <a href="{{route('mailingList.delete.list', $value['remote_id'])}}">Delete</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('scripts')
    <script>
        $('.save_list').click(function (e) {
            e.preventDefault();
            $.ajax({
                url: '/marketing/mailinglist/create',
                type: 'POST',
                data: {
                    name: $('.name').val(),
                    service_id: $('#service_id').val(),
                    websites_id: $('#websites_id').val(),
                    email : $('#email').val(),
                },
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (data) {
                    if (true) {
                        location.reload();
                    }
                }
            });
        })
    </script>
@endsection