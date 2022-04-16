@extends('layouts.app')


@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <style type="text/css">
        .permission_tableWrapper {
            max-width: 600px;
            margin: 0 auto;
        }
        #permission_table th, td {
            white-space: nowrap;
        }

        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting:before,
        table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_asc:before,
        table.dataTable thead .sorting_asc_disabled:after,
        table.dataTable thead .sorting_asc_disabled:before,
        table.dataTable thead .sorting_desc:after,
        table.dataTable thead .sorting_desc:before,
        table.dataTable thead .sorting_desc_disabled:after,
        table.dataTable thead .sorting_desc_disabled:before {
            bottom: .5em;
        }
        .but{
            background-color: lightblue;
            border-radius: 29px;
            border: 0;
        }
        #app{
            overflow: scroll !important;
        }
    </style>

@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
                <h2 class="page-heading"> Vendor Category Permission To Users</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="alert alert-success" id="success_alert" style="display: none;">
                <p></p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 pl-5 pr-5">
            <div class="form-group">
                <div class="table-wrapper-scroll-y my-custom-scrollbar">

                    <table id="permission_table" class=" dfdgfd table table-striped table-bordered table-sm" cellspacing="0" style="table-layout: fixed;" >
                        <thead>
                        <tr>
                            <th style="width:45px">Sr</th>
                            <th style="width:80px" class="Website-task">Users </th>
                            @foreach($categories as $category)
                                <th style="width:70px" class="Website-task">{{ $category->title }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{++$i }}</td>
                                <td class="Website-task"><a style="color:black;" href="{{ url("/users/$user->id/edit") }}">{{ $user->name }} ({{ count($user->permissions) }})</a></td>
                                @foreach($categories as $category)
                                    <td class="Website-task p-0 pl-3">
                                        @if(in_array($category->id, $user->vendorCategoryPermission->pluck('id')->toArray()))
                                            <input type="checkbox" name="permission_check" checked class="permission-check" data-user-id="{{$user->id}}" data-category-id="{{$category->id}}" style="margin: 3px 0 0;">
                                        @else
                                            <input type="checkbox" name="permission_check" class="permission-check" data-user-id="{{$user->id}}" data-category-id="{{$category->id}}"style="margin: 3px 0 0;">
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                   
                </div>
                {!! $users->render() !!}
            </div>
        </div>

    </div>
@endsection

@section('scripts')

    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        // $(document).ready(function () {
        //     $('#permission_table').DataTable({
        //         "scrollX": true
        //     });
        //     $('.dataTables_length').addClass('bs-select');
        // });

        $(document).on('change', '.permission-check', function() {
            var catId = $(this).data('category-id');
            var userId = $(this).data('user-id');
            var check = 0;
            if($(this).prop("checked") == true){
                check = 1;
            }
            $.ajax({
                type: "POST",
                url: "{{ route('vendor-category.update.permission') }}",
                data: {"_token": "{{ csrf_token() }}","user_id": userId , "category_id" : catId ,"check" : check },
                dataType: "json",
                success: function(message) {
                    $('#success_alert p').text(message.message);
                    $('#success_alert').show();
                    // location.reload(true);
                }, error: function(){
                    alert('Failed adding Permission');
                }

            });
        });
    </script>
@endsection
