@extends('layouts.app')

@section('large_content')
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput-typeahead.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />
    <script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>

    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }

        tr th {
            width: 20px;
        }

        .nav-item a {
            color: #555;
        }

        a.btn-image {
            padding: 2px 2px;
        }

        .text-nowrap {
            white-space: nowrap;
        }

        .search-rows .btn-image img {
            width: 12px !important;
        }

        .search-rows .make-remark {
            border: none;
            background: none
        }

        table.csvData-table tbody tr td div{
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            max-width: 150px;
        }

        table.csvData-table tbody tr td .bg-success{
            padding: 5px !important;
        }

        table.csvData-table tbody tr td .bg-custom-grey{
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            max-width: 150px;
        }
        .csvData-table tbody td {
            padding-bottom: 10px !important;
        }

        .viewbtn_model {
            top: 5px !important;
        }

        .history_model {
            top: 10px;
        }

        @media (max-width: 1280px) {
            table.table {
                width: 0px;
                margin: 0 auto;
            }

            /** only for the head of the table. */
            table.table thead th {
                padding: 10px;
            }

            /** only for the body of the table. */
            table.table tbody td {
                padding: 10 px;
            }

            .text-nowrap {
                white-space: normal !important;
            }
        }
    </style>
@endsection

<div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>
<div class="row">
    <div class="col-md-12 p-0">
        <h2 class="page-heading">GoogleFile Translator Languages List</h2>
    </div>
</div>
<div class="row">
@if(session()->has('success'))
    <div class="alert alert-success w-100">
        {{session()->get('success')}}
    </div>
    @endif

</div>
<div class="row w-100">
    <div class="col-md-2">
       {{-- <label>Language</label> 
       <select class="form-control" name="lang_filter" id="lang_filter">
            <option value="">Select</option>
            <option value="en">EN</option>
            <option value="es">ES</option>
            <option value="ru">RU</option>
            <option value="ko">KO</option>
            <option value="ja">JA</option>
            <option value="it">IT</option>
            <option value="de">DE</option>
            <option value="fr">FR</option>
            <option value="nl">NL</option>
            <option value="zh">ZH</option>
            <option value="ar">AR</option>
            <option value="ur">UR</option>
        </select> --}}
    </div>
    <div class="col-md-2">
    {{-- <label>Status</label>
        <select class="form-control" name="status_filter" id="status_filter">
            <option value="">Status</option>
            <option value="checked">checked</option>
            <option value="unchecked">unchecked</option>
            <option value="">others</option>
        </select> --}}
    </div>
    <div class="col-md-2">
    {{-- <label>Users</label>
        <select class="form-control" name="users_filter" id="users_filter">
            <option value="">Select</option>
            @php
            use App\User;    
            @endphp
            @foreach (User::all() as $users)
                <option value="{{$users->id}}">{{$users->name}}</option>
            @endforeach
        </select> --}}
    </div>
    <div class="col-md-1 my-5">
    {{-- <a href="#" class="filterSearch">
            <i class="fa fa-search"></i>
        </a> --}}
    </div>

</div>


<div class="modal fade" id="permissions_model" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">Edit Permission</h4>
            </div>
            <div class="modal-body">
                <form class="permission_form">
                    <div class="form-group">
                        <label>Select User :</label>
                        <select class="form-control" id="selectUserId" name="user">
                            <option>Select</option>
                            @foreach (App\User::where('is_active', '1')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Lanuage</label>
                        <select class="form-control" name="lang" id="selectLangId">
                            <option>Select</option>
                            <option value="en">EN</option>
                            <option value="es">ES</option>
                            <option value="ru">RU</option>
                            <option value="ko">KO</option>
                            <option value="ja">JA</option>
                            <option value="it">IT</option>
                            <option value="de">DE</option>
                            <option value="fr">FR</option>
                            <option value="nl">NL</option>
                            <option value="zh">ZH</option>
                            <option value="ar">AR</option>
                            <option value="ur">UR</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Action</label>
                        <select class="form-control" name="action" id="actionId">
                            <option>Select</option>
                            <option value="view">view</option>
                            <option value="edit">edit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Permission Type</label>
                        <select class="form-control" name="type" id="typeId">
                            <option>Select</option>
                            <option value="basic">Basic</option>
                            <option value="advance">Advance</option>
                        </select>
                    </div>
                    <div class="d-none alert alert-class">

                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-permission-submit-form">Add
                    Permission</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>



<div class="mt-3 col-md-12">
    <table class="table table-bordered table-striped" id="log-table">
        <thead>
            <tr>
                <th width="3%">ID</th>
                <th width="30%">Filepath</th>
                <th width="3%">Action</th>
                <th width="10%">Date</th>
             </tr>
            <tbody>
                <?php $id = request()->route('id'); ?>
                @foreach ($filenames as $key => $file)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td><div class="show_csv_co">{{$file->filename}}</div> 
                        </td>
                        <td><button type="button" id="ip_log" class="btn btn-secondary process-magento-push-btn" title="pushCsvDownlaod" data-filename="{{$file->filename}}"data-id="{{$id}}"> 
                            <i class="fa fa-upload  upload_faq" aria-hidden="true"></i></button></td>
                        <td><div class="show_csv_co">{{$file->created_at}}</td>  
                    </tr>                        
                @endforeach
            </tbody>
        </thead>
    </table>
</div>


<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
50% 50% no-repeat;display:none;">

@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
</script>
<script type="text/javascript">
    
    $(document).on("click", ".process-magento-push-btn", function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            var filename = $(this).data("filename");

            $.ajax({
                url: '{{route('store-website.single.push.command.run')}}',
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, 
                data: {
                    id: id,
                    filename:filename,
                },
                beforeSend: function() {
                    $('#loading-image').show();
                },
            }).done(function(response) {
                if (response.code == '200') {
                    toastr['success']('Command Run successfully!!!', 'success');
                } else if(response.code == '500') {
                    toastr['error'](response.message, 'error');
                }
                else {
                    toastr['error'](response.message, 'error');
                }
                $('#loading-image').hide();
            }).fail(function(errObj) {
                $('#loading-image').hide();
                    toastr['error']("Invalid JSON response", 'error');

            });
         });



</script>
@endsection
