@extends('layouts.app')

@section('favicon' , 'instagram.png')

@section('title', 'Influencer Info')

@section('styles')
<style type="text/css">
         #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection
@section('large_content')
    <div id="myDiv">
      <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="row">
        <div class="col-md-12">
           <h2 class="page-heading">Influencers (<span id="total">{{ $influencers->total() }}</span>)</h2>
            <div class="pull-left">
                <div class="row">
                <div class="form-group mr-3 mb-3">
                        <input name="term" type="text" class="form-control global" id="term"
                               value="{{ isset($term) ? $term : '' }}"
                               placeholder="name">
                </div>
                <div class="form-group mr-3 mb-3">
                    <button type="button" class="btn btn-image" onclick="resetSearch()"><img src="/images/clear-filters.png"/></button> 
                </div>
                </div>
            </div>
            <div class="pull-right">
                <div class="row">
                <div class="form-group mr-3 mb-3">
                        <input name="name" type="text" class="form-control" id="keywordname"
                               placeholder="New Keyword">
                </div>
                <div class="form-group mr-3 mb-3">
                    <button type="button" class="btn btn-image" onclick="submitKeywork()"><img src="/images/add.png"/></button> 
                </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            @if(Session::has('message'))
                <div class="alert alert-success">
                    {{ Session::get('message') }}
                </div>
            @endif
        </div>
        <div class="col-md-12">
             <div class="row">
        <div class="col-md-12">
            <div class="panel-group">
                <div class="panel mt-5 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" href="#collapse1">Keywords</a>
                        </h4>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse">
                        <div class="panel-body">
                           
                            <table class="table table-bordered table-striped" id="phone-table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                               @foreach($keywords as $keyword)     
                                <tr>
                                   <td>{{ $keyword->name }}</td>
                                   <td><button class="btn btn-link" onclick="getImage('{{ $keyword->name }}')" data-toggle="tooltip" data-placement="top" title="Image From Scrapper"><i class="fa fa-barcode"></i></button>
                                   <button  class="btn btn-link" title="Get Status" onclick="getStatus('{{ $keyword->name }}')" title="Get Status Of Scrapper"><i class="fa fa-history" aria-hidden="true"></i></button> 
                                   <button class="btn btn-link" onclick="startScript('{{ $keyword->name }}')" data-toggle="tooltip" data-placement="top" title="Start Script"><i class="fa fa-refresh"></i></button> 
                                   <button class="btn btn-link" onclick="getLog('{{ $keyword->name }}')" data-toggle="tooltip" data-placement="top" title="Get Log From Server"><i class="fa fa-history"></i></button>
                                   <button class="btn btn-link" onclick="restartScript('{{ $keyword->name }}')" data-toggle="tooltip" data-placement="top" title="Restart Script From Server"><i class="fa fa-stop"></i></button> 
                                   <button class="btn btn-link" onclick="stopScript('{{ $keyword->name }}')" data-toggle="tooltip" data-placement="top" title="Stop Script From Server"><i class="fa fa-stop"></i></button> 
                                   </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
            <div class="table-responsive">
                <table class="table-striped table table-bordered" id="data-table">
                    <thead >
                    <tr>
                        <th>Username</th>
                        <th>Posts</th>
                        <th>Phone</th>
                        <th>Website</th>
                        <th>Twitter</th>
                        <th>Facebook</th>
                        <th>Country</th>
                        <th>Followers</th>
                        <th>Following</th>
                        <th>Description</th>
                        <th>Keyword</th>
                    </tr>
                   </thead>
                     <tbody>
                   @include('instagram.hashtags.partials.influencer-data')
                    </tbody>
                </table>
                
                 {!! $influencers->render() !!}
            </div>
        </div>

        
    </div>
@endsection
@include("marketing.whatsapp-configs.partials.image")
@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

         $(document).ready(function() {
        src = "{{ route('influencers.index') }}";
        $(".global").autocomplete({
        source: function(request, response) {
            term = $('#term').val();
            
            
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    term : term,
                },
                beforeSend: function() {
                       $("#loading-image").show();
                },
            
            }).done(function (data) {
                 $("#loading-image").hide();
                console.log(data);
                $('#total').val(data.total)
                $("#data-table tbody").empty().html(data.tbody);
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

     function resetSearch(){
         blank = '';
         $.ajax({
                url: src,
                dataType: "json",
                data: {
                    blank : blank,
                },
                beforeSend: function() {
                       $("#loading-image").show();
                },
            
            }).done(function (data) {
                 $("#loading-image").hide();
                console.log(data);
                $('#total').val(data.total)
                $("#data-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
     } 

     function submitKeywork() {
           name = $('#keywordname').val();
           $.ajax({
               url: '{{ route('influencers.keyword.save') }}',
               type: 'POST',
               dataType: 'json',
               data: {
                    name: name,
                    "_token": "{{ csrf_token() }}",
                },
           })
           .done(function(response) {
               $('#keywordname').val('')
               alert(response.message);
               location.reload();
           })
           .fail(function() {
               console.log("error");
           });
           
        } 

     function getImage(name) {
              $.ajax({
               url: '{{ route('influencers.image') }}',
               type: 'POST',
               dataType: 'json',
               data: {
                    name: name,
                    "_token": "{{ csrf_token() }}",
                },
               })
               .done(function(response) {
                    if(response.success == true){
                        $('#image_crop').attr('src',response.message);
                        $('#largeImageModal').modal('show');
                    }else{
                        alert(response.message)
                    }
                    
               })
               .fail(function() {
                   console.log("error");
               });
          }
        function getStatus(name) {
              $.ajax({
               url: '{{ route('influencers.status') }}',
               type: 'POST',
               dataType: 'json',
               data: {
                    name: name,
                    "_token": "{{ csrf_token() }}",
                },
               })
               .done(function(response) {
                   alert(response.message);
               })
               .fail(function() {
                   console.log("error");
               });
          }
          function startScript(name) {
            var result = confirm("You Want to start this script "+name+"?");
            if(result){
                $.ajax({
                   url: '{{ route('influencers.start') }}',
                   type: 'POST',
                   dataType: 'json',
                   data: {
                        name: name,
                        "_token": "{{ csrf_token() }}",
                    },
                   })
                   .done(function(response) {
                       alert(response.message);
                   })
                   .fail(function() {
                       console.log("error");
                }); 
            }
             
          }

          function getLog(name) {
            var result = confirm("You Want the log for this script "+name+"?");
            if(result){
                $.ajax({
                   url: '{{ route('influencers.log') }}',
                   type: 'POST',
                   dataType: 'json',
                   data: {
                        name: name,
                        "_token": "{{ csrf_token() }}",
                    },
                   })
                   .done(function(response) {
                       if(response.message == 'No Logs Available'){
                          alert(response.message);
                       }else{
                          openInNewTab(response.message)
                       } 
                   })
                   .fail(function() {
                       console.log("error");
                }); 
            }
             
          }
          function restartScript(name) {
            var result = confirm("You Want to re-start this script "+name+"?");
            if(result){
                $.ajax({
                   url: '{{ route('influencers.restart') }}',
                   type: 'POST',
                   dataType: 'json',
                   data: {
                        name: name,
                        "_token": "{{ csrf_token() }}",
                    },
                   })
                   .done(function(response) {
                       alert(response.message);
                   })
                   .fail(function() {
                       console.log("error");
                }); 
            }
             
          }
          function stopScript(name) {
            var result = confirm("You Want to stop this script "+name+"?");
            if(result){
                $.ajax({
                   url: '{{ route('influencers.stop') }}',
                   type: 'POST',
                   dataType: 'json',
                   data: {
                        name: name,
                        "_token": "{{ csrf_token() }}",
                    },
                   })
                   .done(function(response) {
                       alert(response.message);
                   })
                   .fail(function() {
                       console.log("error");
                }); 
            }
             
          } 

          function openInNewTab(url) {
            var win = window.open(url, '_blank');
            win.focus();
          }       
    </script>

@endsection