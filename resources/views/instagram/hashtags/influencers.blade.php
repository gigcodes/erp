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

        .chat-righbox a{
            color: #555 !important;
            font-size: 18px;
        }
        .type_msg.message_textarea {
            width: 90%;
            height: 60px;
        }
        .cls_remove_rightpadding{
            padding-right: 0px !important;
        }
        .cls_remove_leftpadding{
            padding-left: 0px !important;
        }
        .cls_remove_padding{
            padding-right: 0px !important;
            padding-left: 0px !important;
        }
        .cls_quick_commentadd_box{
            padding-left: 5px !important;   
            margin-top: 3px;
        }
        .cls_quick_commentadd_box button{
            font-size: 12px;
            padding: 5px 9px;
            margin-left: -8px;
            background: none;
        }
        .send_btn {
            margin-left: -5px; 
        }
        .cls_message_textarea{
            height: 35px !important;
            width: 100% !important;
        }
        .cls_quick_reply_box{
            margin-top: 5px;
        }
        .cls_addition_info {
            padding: 0px 0px;
            margin-top: -8px;
        }
        #circle {
        background: green;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .pd-2 {
        padding:2px;
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
            <form class="form-inline" action="{{ route('influencers.index') }}" method="GET">
                <div class="row">
                <div class="form-group mr-3 mb-3">
                        <input name="term" type="text" class="form-control" id="term"
                               value="{{ isset($term) ? $term : '' }}"
                               placeholder="Search...">
                </div>
                <div class="form-group mr-3 mb-3">
                        <select class="form-control" name="posts" id="">
                            <option value="">Posts</option>
                            <option value="10" {{$posts == 10 ? 'selected' : ''}}>Greater than 10</option>
                            <option value="20" {{$posts == 20 ? 'selected' : ''}}>Greater than 20</option>
                            <option value="30" {{$posts == 30 ? 'selected' : ''}}>Greater than 30</option>
                            <option value="50" {{$posts == 50 ? 'selected' : ''}}>Greater than 50</option>
                            <option value="100" {{$posts == 100 ? 'selected' : ''}}>Greater than 100</option>
                        </select>
                </div>
                <div class="form-group mr-3 mb-3">
                        <select class="form-control" name="followers" id="">
                            <option value="">Followers</option>
                            <option value="10" {{$followers == 10 ? 'selected' : ''}}>Greater than 10</option>
                            <option value="20" {{$followers == 20 ? 'selected' : ''}}>Greater than 20</option>
                            <option value="30" {{$followers == 30 ? 'selected' : ''}}>Greater than 30</option>
                            <option value="50" {{$followers == 50 ? 'selected' : ''}}>Greater than 50</option>
                            <option value="100" {{$followers == 100 ? 'selected' : ''}}>Greater than 100</option>
                        </select>
                </div>
                <div class="form-group mr-3 mb-3">
                        <select class="form-control" name="following" id="">
                            <option value="">Following</option>
                            <option value="10" {{$following == 10 ? 'selected' : ''}}>Greater than 10</option>
                            <option value="20" {{$following == 20 ? 'selected' : ''}}>Greater than 20</option>
                            <option value="30" {{$following == 30 ? 'selected' : ''}}>Greater than 30</option>
                            <option value="50" {{$following == 50 ? 'selected' : ''}}>Greater than 50</option>
                            <option value="100" {{$following == 100 ? 'selected' : ''}}>Greater than 100</option>
                        </select>
                </div>
                <div class="form-group mr-3 mb-3">
                <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>
                    <!-- <button type="button" class="btn btn-image" onclick="resetSearch()"><img src="/images/clear-filters.png"/></button>  -->
                </div>
                </div>
                </form>
            </div>
            <div class="pull-right">
                <div class="row">
                <div class="form-group mr-3 mb-3">    
                    <button class="btn btn-secondary btn-sm" onclick="sortData()">Sort Data</button> 
                </div>        
                <div class="form-group mr-3 mb-3">
                     <input name="name" type="text" class="form-control" id="keywordname" placeholder="New Keyword">
                </div>
                <div class="form-group mr-3 mb-3">
                     <?php echo Form::select('instagram_account_id',["" => "N/A"] + App\Marketing\InstagramConfig::pluck('username','id')->toArray(),null, ["class" => "form-control","id" => 'instagram_account_id']); ?>
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
                                    <th>Account</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                               @foreach($keywords as $keyword)     
                                <tr data-keyword-id="{{$keyword->id}}" data-keyword-name="{{ $keyword->name }}">
                                   <td>{{ $keyword->name }}</td>
                                   <td>
                                        <div class="form-group mr-3 mb-3">
                                             <?php echo Form::select('instagram_account_id',["" => "N/A"] + App\Marketing\InstagramConfig::pluck('username','id')->toArray(),$keyword->instagram_account_id, ["class" => "form-control","id" => 'instagram_account_id_change']); ?>
                                        </div>
                                   </td>
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
    
<div class="row" style="padding:0px;margin:0px;">
        <div class="col-md-12" style="padding:0px;">
            <div class="pull-right">
              <a href="#" class="btn btn-xs direct-message btn-secondary">
                            Message
              </a>
            </div>
        </div>
    </div>
            <div class="table-responsive">
                <table class="table-striped table table-bordered" id="data-table" style="table-layout:fixed;">
                    <thead >
                    <tr>

                        <th style="width:7%">Date</th>
                        <th style="width:10%">Username</th>
                        <th style="width:10%">Email</th>
                        <th style="width:10%">Hashtag</th>
                        <th style="width:5%">Posts</th>
                        <th style="width:7%">Followers</th>
                        <th style="width:7%">Following</th>
                        <th style="width:7%">Country</th>
                        <th style="width:10%">Description</th>
                        <th style="width:21%">Communication</th>
                        <th style="width:6%">Action</th>
                        <!-- <th>Phone</th>
                        <th>Website</th>
                        <th>Twitter</th>
                        <th>Facebook</th>
                        <th>Keyword</th> -->
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

    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog" style="width: 1000px; max-width: 1000px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;" id="direct-modal-chat">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div id="directMessageModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content ">
      <div class="modal-header">
                    <h4 class="modal-title">Direct message</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" id="directMessageForm" method="POST">
                    @csrf
                    <div class="modal-body">
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <strong>Account:</strong>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                    <select class="form-control account-search select2" name="account_id" data-placeholder="Sender...">
                                        <option value="">Select sender...</option>
                                        @foreach ($accounts as $key => $account)
                                            <option value="{{ $key }}">{{ $account }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <strong>Message:</strong>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                    <textarea cols="45" class="form-control" name="message"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Send</button>
                    </div>
                </form>
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

        $('select.select2').select2({
                tags: true,
                width: "100%"
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

     $(document).on("change","#instagram_account_id_change",function() {
           name = $(this).closest("tr").data("keyword-name");
           instagram_account_id = $(this).val();
           $.ajax({
               url: '{{ route('influencers.keyword.save') }}',
               type: 'POST',
               dataType: 'json',
               data: {
                    name: name,
                    instagram_account_id: instagram_account_id,
                    "_token": "{{ csrf_token() }}",
                },
           })
           .done(function(response) {
               alert(response.message);
               //location.reload();
           })
           .fail(function() {
               console.log("error");
           });
     }); 

     function submitKeywork() {
           name = $('#keywordname').val();
           instagram_account_id = $('#instagram_account_id').val();
           $.ajax({
               url: '{{ route('influencers.keyword.save') }}',
               type: 'POST',
               dataType: 'json',
               data: {
                    name: name,
                    instagram_account_id: instagram_account_id,
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
          
          $(document).on('click', '.expand-row-msg', function () {
            var name = $(this).data('name');
			var id = $(this).data('id');
            var full = '.expand-row-msg .show-short-'+name+'-'+id;
            var mini ='.expand-row-msg .show-full-'+name+'-'+id;
            $(full).toggleClass('hidden');
            $(mini).toggleClass('hidden');
        });

        $(document).on('click', '.send_btn', function () {
			var id = $(this).data('id');
            var account_id = $(".account-search-"+id).val();
            var message = $('#message'+id).val();
            $.ajax({
                url: '{{ route('direct.send-message') }}',
                type: 'POST',
                dataType: 'json',
                data: {
                            "_token": "{{ csrf_token() }}", 
                            "message" : message,
                            "influencer_id" : id,
                            "account_id" : account_id,
                       },
                    })
                    .done(function() {
                        $('#message'+id).val('');
                        toastr['success']('Successfull', 'success');
                    })
                    .fail(function(error) {
                        toastr['error'](error.responseJSON.message, 'error');
                    })
        });


        
        function sendMessage(id){
                message = $('#message'+id).val();
                if(sendMessage){
                    $.ajax({
                        url: '{{ route('direct.send') }}',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            "_token": "{{ csrf_token() }}", 
                            "message" : message,
                            "thread_id" : id,
                       },
                    })
                    .done(function() {
                        $('#message'+id).val('');
                        console.log("success");
                    })
                    .fail(function() {
                        console.log("error");
                    })
                    .always(function() {
                        console.log("complete");
                    });
                    
                }else{
                    alert('Please Select Text')
                }
            }

        var selectedInfluencers = [];
         $(document).on('click', '.selectedInfluencers', function () {
            var checked = $(this).prop('checked');
            var id = $(this).val();
             if (checked) {
                selectedInfluencers.push(id);
            } else {
                var index = selectedInfluencers.indexOf(id);
                selectedInfluencers.splice(index, 1);
            }
        });

        $(document).on("click",".direct-message",function(e){
          e.preventDefault();
          if(selectedInfluencers.length < 1) {
            toastr['error']("Select few influencers first");
            return;
          }
          $('#directMessageModal').modal('show');
        });

        $(document).on('submit', '#directMessageForm', function (e) {
                e.preventDefault();
                var data = $(this).serializeArray();
                var account_id = $('.account-search').val();
                data.push({name: 'selectedInfluencers', value: selectedInfluencers});
                $.ajax({
                    url: "{{route('direct.group-message')}}",
                    type: 'POST',
                    data: data,
                    success: function (response) {
                        toastr['success']('Successful', 'success');
                        $('#directMessageModal').modal('hide');
                        $("#directMessageForm").trigger("reset");
                        $("#data-table tr").find('.selectedInfluencers').each(function () {
                          if ($(this).prop("checked") == true) {
                            $(this).prop("checked", false);
                          }
                        });
                        selectedInfluencers = [];
                    },
                    error: function (error) {
                        toastr['error'](error.responseJSON.message, 'error');
                    }
                });
        });

        $(document).on('click', '.load-direct-chat-model', function () {
            $.ajax({
                url: '{{ route('direct.infulencers-messages') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        id : $(this).data("id")
                    },
                beforeSend: function() {
                    
                },

                }).done(function (data) {
                    $('#direct-modal-chat').empty().append(data.messages);
                    $('#chat-list-history').modal('show');
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });

            });
            $(document).on('click', '.latest-post', function () {
            $.ajax({
                url: '{{ route('direct.latest-posts') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        id : $(this).data("id")
                    },
                beforeSend: function() {
                    $("#loading-image").show();
                },

                }).done(function (data) {
                    $("#loading-image").hide();
                }).fail(function (error) {
                    console.log(error);
                    $("#loading-image").hide();
                    toastr['error'](error.responseJSON.message);
                });

            });

            $(document).on('click', '.expand-row-btn', function () {
            var id = '#expand-'+$(this).data('id');
            console.log($(this).data('id'));
            console.log(id);
            $(id).toggleClass('dis-none');
        });


        function sortData() {
                
                $.ajax({
                url: '{{ route('influencers.sort') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                    },
                beforeSend: function() {
                    $("#loading-image").show();
                },

                }).done(function (data) {
                    $("#loading-image").hide();
                    toastr['success'](data);
                }).fail(function (error) {
                    console.log(error);
                    $("#loading-image").hide();
                    toastr['error'](error);
                });

            
        }    
    </script>

@endsection