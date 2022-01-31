@extends('layouts.app')

@section('styles')
    
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
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
       <img id="loading-image" src="{{env('APP_URL')}}/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading"> Configs</h2>
            <!-- <div class="pull-left">
                <form action="{{ route('whatsapp.config.index') }}" method="GET" class="form-inline align-items-start">
                    <div class="form-group mr-3 mb-3">
                        <input name="term" type="text" class="form-control global" id="term"
                               value="{{ isset($term) ? $term : '' }}"
                               placeholder="number , provider, username">
                    </div>
                    <div class="form-group ml-3">
                        <div class='input-group date' id='filter-date'>
                            <input type='text' class="form-control global" name="date" value="{{ isset($date) ? $date : '' }}" placeholder="Date" id="date" />

                            <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                        </div>
                    </div>


                    <button type="submit" class="btn btn-image"><img src="{{env('APP_URL')}}//images/filter.png" /></button>
                </form>
            </div> -->
            <div class="pull-right">
              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#ConfigCreateModal">+</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @include("social.header_menu")
      
    <div class="table-responsive mt-3">
      <table class="table table-bordered" id="passwords-table">
        <thead>
          <tr>
            <!-- <th style="width: 3% !important;">ID</th> -->
            <th style="width: 5% !important;">Website</th>
            <th style="width: 5% !important;">Platform</th>
            <th style="width: 5% !important;">Name</th>
            <th style="width: 5% !important;">UserName</th>
            <th style="width: 5% !important;">Status</th>
            <th style="width: 5% !important;">Started At</th>
            <th style="width: 5% !important;">Actions</th>
          </tr>

          <!-- <tr>
            <th style="width: 3% !important;"><input type="text" id="username" class="search form-control"></th>
            <th style="width: 3% !important;"></th>
            <th style="width: 3% !important;"><input type="text" id="number" class="search form-control"></th>
            <th style="width: 3% !important;"><input type="text" id="provider" class="search form-control"></th>
            <th style="width: 3% !important;"></th>
            
            <th style="width: 3% !important;"></th> 
          </tr> -->
        </thead>

        <tbody>

       @include('social.configs.partials.data') 

          {!! $socialConfigs->render() !!}
          
        </tbody>
      </table>
    </div>

@include('social.configs.partials.add-modal')
   
@endsection


@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
      <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>

        $(document).ready(function() {
            $(".select-multiple").multiselect();
            $(".select-multiple2").select2();
        });

        $('#filter-date').datetimepicker({
            format: 'YYYY-MM-DD',
        });

        $('#filter-whats-date').datetimepicker({
            format: 'YYYY-MM-DD',
        });


        // $('.date').change(function(){
        //     alert('date selected');
        // });


    function changesocialConfig(config_id) {
      //  alert("ter");
        $("#ConfigEditModal"+ config_id +"" ).modal('show');
    }
    
    function deleteConfig(config_id) {
        event.preventDefault();
        if (confirm("Are you sure?")) {
             $.ajax({
            type: "POST",
            url: "{{ route('social.config.delete') }}",
            data: {"_token": "{{ csrf_token() }}", "id": config_id},
            dataType: "json",
            success: function (message) {
               alert('Deleted Config');
               location.reload(true);
            }, error: function () {
               alert('Something went wrong');
            }

        });
        }
        return false;
       
    }

        $(document).ready(function() {
        src = "{{ route('social.config.index') }}";
        $(".search").autocomplete({
        source: function(request, response) {
            // number = $('#number').val();
            // username = $('#username').val();
            // provider = $('#provider').val();
            // customer_support = $('#customer_support').val();
          

            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    // number : number,
                    // username : username,
                    // provider : provider,
                    // customer_support : customer_support,
                
                },
                beforeSend: function() {
                       $("#loading-image").show();
                },
            
            }).done(function (data) {
                 $("#loading-image").hide();
                console.log(data);
                $("#passwords-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        },
        minLength: 1,
       
        });
    });


  

</script>
@endsection