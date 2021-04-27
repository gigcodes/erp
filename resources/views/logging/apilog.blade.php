@extends('layouts.app')

@section('title', 'Laravel API Log List')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
            <h2 class="page-heading">Laravel API Logs (<span class="page-total">{{$count}}</span>)</h2>
             <!-- <div class="pull-right">
                <a href="/logging/live-laravel-logs" type="button" class="btn btn-secondary">Live Logs</a>
                <button type="button" class="btn btn-image" onclick="refreshPage()"><img src="/images/resend2.png" /></button>
            </div> -->

        </div>
    </div>

    @include('partials.flash_messages')

    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th width="10%">ID</th>
                <th width="10%">IP</th>

                <th width="10%">Method</th>
                <th width="25%">URL</th>
                <th width="20%">Request</th>
                <th width="5%">Status Code</th>
                <th width="5%">Time Taken</th>
              
                <th width="10%">Created At</th>
                <th width="10%">Action</th>

            </tr>
            <tr>
                
                <th width="10%"><input type="text" class="search form-control tbInput" name="id" id="filename"></th>
                <th width="10%"><input type="text" class="search form-control tbInput" id="log" name="ip"></th>
                <th width="10%"><input type="text" name="method" class="search form-control tbInput" id="website"></th>
                
                <th width="10%"><input type="text" name="url" class="search form-control tbInput" id="moduleName"></th>


                <th></th>
               <!--  <th width="10%"><input type="text" class="search form-control" id="controllerName"></th> -->
                <th width="10%"><input type="text" name="status" class="search form-control tbInput" id="action"></th>
                <th></th>


                <th> <div class='input-group' id='log-created-date'>
                        <input type='text' class="form-control" name="created_at" value="" placeholder="Date" autocomplete="off" />
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                </th>
                 <th></th>
                <!-- <th> <div class='input-group' id='created-date'>
                        <input type='text' class="form-control " name="phone_date" value="" placeholder="Date" id="created_date" />
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                </th>
                <th> <div class='input-group' id='updated-date'>
                        <input type='text' class="form-control " name="phone_date" value="" placeholder="Date" id="updated_date" />
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </th> -->
            </tr>
            </thead>

            <tbody id="content_data" class="tableLazy">
             @include('logging.partials.apilogdata')
            </tbody>

            

        </table>
    </div>

    <div class="modal fade" id="api_response_modal" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">View Response</h4>
        </div>
        <div class="modal-body">
            <label>Reponse</label>
         <pre style="overflow:scroll;max-height:350px;" id="json"></pre>
         <label>Request</label>
         <pre style="overflow:scroll;max-height:100px;" id="json_request"></pre>

         
        </div>
        <div class="modal-footer">
         
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
 

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    
    <script type="text/javascript">



    //Ajax Request For Search
    $(document).ready(function () {


        $(document).on('keyup','.tbInput',function()
        {
           
            filterResults();
           // console.log(data);
        })
          
        
        //Expand Row
       
        $('#log-created-date').datetimepicker(
            { format: 'YYYY/MM/DD' }).on('dp.change', 
            function (e) 
            {
            
             var formatedValue = e.date.format(e.date._f);
                created = $('#created_date').val();

                
            filterResults();

                
                

            }) 
             
   



    
       

         $(window).on('scroll',function()
         {
            
    if($(window).scrollTop() == $(document).height() - $(window).height()) {
           var page_no=$('.currentPage').last().attr('data-page');

           page_no=parseInt(page_no)+1;
 //console.log(page_no);

 var row= getFilterValues();

 

 $.ajax({
                    url: '{{route("api-log-list")}}'+'?page='+page_no,
                    dataType: "json",
                    data: row,
                    method:'post',
                    beforeSend: function () {
                        
                    },

                }).done(function (res) {
                    
                      $('#noresult_tr').remove();
              //var res=JSON.parse(res);

              if(res.status){
              $('.tableLazy').append(res.html);
              $(".page-total").html(res.count);
              $.each(res.logs.data,function(k,v)
              {
                $logsRecords.push(v);
              })
              
            
           }
              else
                $('.tableLazy').append(res.html)
           })

             
          
    }

         })

         function filterResults()
         {
                      $('#noresult_tr').remove();
             
             var row= getFilterValues();
            
           
            $.ajax({
                    url: '{{route("api-log-list")}}',
                    dataType: "json",
                    data: row,
                    method:'post',
                    beforeSend: function () {
                        
                    },

                }).done(function (res) {
                    
                      $('#noresult_tr').remove();
         

              if(res.status){
                $('.tableLazy').html(res.html);


                $logsRecords=res.logs.data;
                $(".page-total").html(res.count);
              
              
           }
              else
                $('.tableLazy').html(res.html)
           })
         } 

         function getFilterValues()
         {
            var row={};
            $('.tbInput').each(function()
            {
                var name=$(this).attr('name');
                
                 row[name]=$(this).val();
                
                //data.push(row);
            })

            row['created_at']=$('[name="created_at"]').val();
            row['_token']='{{csrf_token()}}';

            return row;

         }

         $(document).on('click','.showModalResponse',function()
         {
            var selector=$(this);
            $.each($logsRecords,function(k,v)
            {
               
                if(v.id==selector.attr('data-id'))
                {
                   console.log(v.id);
                    $('#api_response_modal').find('.modal-body').find('#json').html( JSON.stringify(JSON.parse(v.response), undefined, 2));

                     

                     $('#api_response_modal').find('.modal-body').find('#json_request').html( JSON.stringify(JSON.parse(v.request), undefined, 2));
                }
            })
            $('#api_response_modal').modal('show');
         })

         var logsRecords=@json($logs);
         $logsRecords=logsRecords.data;
        // console.log($logsRecords);

     })
    </script>

@endsection