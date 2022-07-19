@extends('layouts.app')
@section('favicon', 'user-management.png')



@section('large_content')

@include('partials.flash_messages')
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;background-color:rgba(255,255,255,0.6);"></div>
<div class="row">
    <div class="col-md-12 p-0">
        <h2 class="page-heading">{{ $title }} <span class="count-text"></span></h2>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <!-- <form class="form-inline message-search-handler" method="post">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php echo Form::text('keyword', request('keyword'), ['class' =>
                            'form-control data-keyword', 'placeholder' => 'Enter keyword']); ?>
                        </div>
                        <div class="form-group">
                            <select name="is_active" class="form-control" placholder="Active:">
                                <option value="0" {{ request('is_active') == 0 ? 'selected' : '' }}>All</option>
                                <option value="1" {{ request('is_active') == 1 ? 'selected' : '' }}>Active
                                </option>
                                <option value="2" {{ request('is_active') == 2 ? 'selected' : '' }}>In active
                                </option>
                            </select>
                        </div>
                        <div class="form-group pl-3">
                            <label for="button">&nbsp;</label>
                            <button style="display: inline-block;width: 10%;margin-top: -16px;" class="btn btn-sm btn-image btn-search-action">
                                <img src="/images/search.png" style="cursor: default;">
                            </button>
                        </div>
                    </div>
                </div>
            </form> -->
        </div>
        <div class="col-lg-12">
            <table id="listUserSchedule" class="table table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th data-data="username" data-name="username" width="15%" data-sortable="false" >Username</th>
                        <th data-data="date" data-name="date" width="10%" data-sortable="false" >Date</th>
                        <?php foreach ($workSlots as $workSlotK => $workSlotV) { ?>
                            <th data-data="{{$workSlotK}}" data-name="{{$workSlotK}}" width="5%" data-sortable="false" >{!! $workSlotV !!}</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include("usermanagement::templates.list-template")
@push("link-css")
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
@endpush
@push("jquery")
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
@endpush
<script>

</script>
<script type="text/javascript" src="{{env('APP_URL')}}/js/jsrender.min.js"></script>
<script type="text/javascript" src="{{env('APP_URL')}}/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="{{env('APP_URL')}}/js/jquery-ui.js"></script>
<script type="text/javascript" src="{{env('APP_URL')}}/js/common-helper.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pnp-sp-taxonomy/1.3.11/sp-taxonomy.es5.umd.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.6/jquery.tagsinput.min.js" integrity="sha512-wTIaZJCW/mkalkyQnuSiBodnM5SRT8tXJ3LkIUA/3vBJ01vWe5Ene7Fynicupjt4xqxZKXA97VgNBHvIf5WTvg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    $('.select-multiple').select2({
        width: '100%'
    });
    $('#due-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
    });

    function loadUsersList() {

        var t = "";
        var ip = "";
        $.ajax({
            url: '{{ route("get-user-list") }}',
            type: 'GET',
            data: {
                _token: "{{ csrf_token() }}",
            },
            dataType: 'json',

            success: function(result) {
                // console.log(result.data);
                t += '<option>Select user</option>';

                $.each(result.data, function(i, j) {
                    t += '<option value="' + i + '">' + j + '</option>'
                });
                t += '<option value="other">Other</option>';

                // console.log(t);
                $("#ipusers").html(t);

                console.log(result.usersystemips);

                $.each(result.usersystemips, function(k, v) {
                    ip += '<tr>';
                    ip += '<td> ' + v.index_txt + ' </td>';
                    ip += '<td> ' + v.ip + '</td>';
                    ip += '<td>' + v.user_id ? v.user.name : v.other_user_name + '</td>';
                    ip += '<td>' + v.notes + '</td>';
                    ip += '<td><button class="btn-warning btn deleteIp" data-usersystemid="' + v.id + '">Delete</button></td>';
                    ip += '</tr>';
                });

                $("#userAllIps").html(ip);

            },
            error: function() {
                // alert('fail');
            }
        });

    }


    var dtblListUserSchedule
    jQuery(document).ready(function() {
        dtblListUserSchedule = jQuery('#listUserSchedule').DataTable({
            lengthChange: false,
            searching: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            paging: true,
            ajax: {
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{ $urlLoadData }}",
                data: function(d) {
                    var extra = {};
                    if (jQuery('#frm-search-crud').length) {
                        var temp = jQuery('#frm-search-crud').serializeArray();
                        for (var i in temp) extra[temp[i].name] = temp[i].value;
                    }
                    return Object.assign(d, extra);
                }
            },
            initComplete: function(settings, json) {
                // applyFeather();
            },
            drawCallback: function(settings) {
                // applyFeather();
            },
            order: [],
        });
    });
</script>
@endsection