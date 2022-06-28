@extends('layouts.app')



@section('title', $title)

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput-typeahead.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
        
        .disabled{
            pointer-events: none;
            background: #bababa;
        }
        .glyphicon-refresh-animate {
            -animation: spin .7s infinite linear;
            -webkit-animation: spin2 .7s infinite linear;
        }

        @-webkit-keyframes spin2 {
            from { -webkit-transform: rotate(0deg);}
            to { -webkit-transform: rotate(360deg);}
        }

        @keyframes spin {
            from { transform: scale(1) rotate(0deg);}
            to { transform: scale(1) rotate(360deg);}
        }
        #CreateCheckList .bootstrap-tagsinput,#EditCheckList .bootstrap-tagsinput{
            display: block;

        }
        #CreateCheckList .modal-body strong ,#EditCheckList .modal-body strong {
            display: block;
            margin-bottom: 5px;
        }
        #CreateCheckList .bootstrap-tagsinput .tag,#EditCheckList .bootstrap-tagsinput .tag  {
            background: gray;
            color: white;
            font-size: 14px;
        }
        .dataTables_scrollHeadInner{
            width: 100% !important; 
        } 
        .dataTables_scrollHeadInner table{
            width: 100% !important; 
        }
        .dataTables_scrollBody table{
            width: 100% !important; 
        }
        .addCheckList{
            display: block;
            width: 100%;
        }
    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
<input type="hidden" value="{{ $id }}" name="checklist_id">
<div class="table-responsive mt-3 pr-2 pl-2">
        @if ($message = Session::get('success'))
            <div class="col-lg-12">
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        <div class="checklist_data">
            <table class="table table-bordered " id="assign_checklist_table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Subjects</th>                        
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        
    </div>   

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).on('click', '#searchReset', function(e) {
            //alert('success');
            $('#dateform').trigger("reset");
            e.preventDefault();
            oTable.draw();
        });

        $('#dateform').on('submit', function(e) {
            e.preventDefault();
            oTable.draw();

            return false;
        });
        // START Print Table Using datatable
        var oTable;
        $(document).ready(function() {
            oTable = $('#assign_checklist_table').DataTable({
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                sScrollX:true,
                searching: false,
                order: [
                    [0, 'desc']
                ],
                targets: 'no-sort',
                bSort: false,
                ajax: {
                    url: "{{ route('checklist.subjects') }}",
                    type: "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: function(d) {
                        d.id = $('input[name=checklist_id]').val();                     
                    },
                },
                columnDefs: [{
                    targets: [],
                    orderable: false,
                    searchable: false
                }],
                columns: [{
                        data: null,
                        width : "5%",
                        render: function (data, type, full, meta) {
                            return meta.row + 1;
                        }
                    },                    
                    {
                        data: 'subjects',
                        width : "50%",
                        name: 'checklist.subjects',
                        render: function(data, type, row, meta) {
                            if(data && data != ""){
                                var subjects = data.split(",");
                                var subject_html = "";
                                for(var i=0; i<subjects.length; i++){
                                    subject_html += "<span class='badge badge-primary mr-2'>"+subjects[i]+"</span>";
                                }
                                return subject_html;
                            }
                            // return data;
                        }
                    }
                ],
            });
        });
        // END Print Table Using datatable

</script>

@endsection
