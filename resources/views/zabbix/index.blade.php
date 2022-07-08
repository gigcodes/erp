@extends('layouts.app')

@section('large_content')

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
            z-index: 60;
        }
	.nav-item a{
		color:#555;
	}
  Route::resource('zabbix', 'ZabbixController');
	a.btn-image{
		padding:2px 2px;
	}
	.text-nowrap{
		white-space:nowrap;
	}
	.search-rows .btn-image img{
		width: 12px!important;
	}
	.search-rows .make-remark
	{
		border: none;
		background: none
	}
  .table-responsive select.select {
    width: 110px !important;
  }


  @media (max-width: 1280px) {
    table.table {
        width: 0px;
        margin:0 auto;
    }

    /** only for the head of the table. */
    table.table thead th {
        padding:10px;
    }

    /** only for the body of the table. */
    table.table tbody td {
        padding:10 px;
    }

    .text-nowrap{
      white-space: normal !important;
    }
  }

</style>
@endsection
<div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
<div class="row">
	<div class="col-md-12 p-0">
		<h2 class="page-heading">Emails List</h2>
	</div>
</div>



<div class="table-responsive mt-3" style="margin-top:20px;">
      <table class="table table-bordered text-nowrap" style="border: 1px solid #ddd;" id="email-table">
        <thead>          
        </thead>
        <tbody>        
         
        </tbody>
      </table>
      <div class="pagination-custom">
       
      </div> 
</div>




@endsection
@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
   var oTable;
        $(document).ready(function() {
            oTable = $('#email-table').DataTable({
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                sScrollX:false,
                searching: false,
               
                targets: 'no-sort',
                bSort: false,
                ajax: {
                    "url": "{{ route('zabbix.index') }}",
                    data: function(d) {
                       
                    },
                },
                columnDefs: [{
                    targets: [],
                    orderable: false,
                    searchable: false
                }],
                columns: [{
                        data: 'hostid',
                        columnName : 'Host ID',
                        render: function (data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        data: 'name',   
                        columnName : 'Host Name',                     
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    // {
                    //     data: 'sub_category_name',
                    //     name: 'checklist.sub_category_name',
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // },{
                    //     data: 'subjects',
                    //     name: 'checklist.subjects',
                    //     render: function(data, type, row, meta) {
                    //         if(data && data != ""){
                    //             var subjects = data.split(",");
                    //             var subject_html = "";
                    //             for(var i=0; i<subjects.length; i++){
                    //                 subject_html += "<span class='badge badge-primary mr-2'>"+subjects[i]+"</span>";
                    //             }
                    //             return subject_html;
                    //         }
                    //         // return data;
                    //     }
                    // },
                    
                ],
            });
        });
    </script>


@endsection

