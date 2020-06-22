@extends('layouts.app')

@section('favicon' , 'instagram.png')

@section('title', 'Instagram Info')

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
           
           <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">HASH TAG MONITORING AND COMMENTING  - MISC ACCOUNTS : @if(isset($hashtag)) #{{ $hashtag->hashtag }} ({{ count($medias) }} Posts) @if(env('INSTAGRAM_MAIN_ACCOUNT') == true) @endif <spam style="color: red;"> ADMIN ACCOUNT PLEASE COMMENT CAREFULLY</spam> @endif</h2>
            <div class="pull-left">
                <form action="/instagram/hashtag/grid/{{ $hashtag->id }}" method="GET" class="form-inline align-items-start">
                    <div class="form-group mr-3 mb-3">
                        <input name="term" type="text" class="form-control global"
                               value="{{ isset($term) ? $term : '' }}"
                               placeholder="username, caption" id="term">
                    </div>
                    <div class="form-group ml-3">
                        <div class='input-group date' id='filter-date'>
                            <input type='text' class="form-control" name="date" value="{{ isset($date) ? $date : '' }}" placeholder="Date" id="date" />

                            <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                        </div>
                    </div>


                    <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                </form>
            </div>
            
        </div>
    </div>
        </div>

        <div class="col-md-2">
            <p>
                <a href="{{ action('HashtagController@index') }}">Show All Targeted Hashtags</a>
            </p>
        </div>

        <div class="col-md-12 mt-2 mb-3">
            <div class="accordion" id="accordionExample">
                <div class="card mt-0" style="width:100%;">
                    <div class="card-header">
                        <div style="cursor: pointer;font-size: 20px;font-weight: bolder;" data-toggle="collapse" data-target="#form_am" aria-expanded="true" aria-controls="form_am">
                          Stats
                        </div>
                    </div>
                    <div id="form_am" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                        <div class="card-body">
                            <table class="table table-striped">
                                <tr>
                                    <th>Narrative</th>
                                    <th>Count</th>
                                </tr>
                                @foreach($stats as $stat)
                                    <tr>
                                        <th>{{ $stat->narrative }}</th>
{{--                                        <th>{{ $stat->year }} - {{ $stat->month }}</th>--}}
                                        <th>{{ $stat->total }}</th>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
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
            <div class="table-responsive">
                <table class="table-striped table table-bordered" id="grid-table">
                    <thead>
                    <tr>
                        <th style="width:1%">SN</th>
                        <th style="width:10%">Hastag</th>
                        <th style="width:5%">User</th>
                        <th style="width: 10%;">Image</th>
                        <th style="width:10%">Caption</th>
                        <th style="width:10%">Communication</th>
                        <th style="width:20%">Comments</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th><input type="text" id="username" class="search form-control" placeholder="Id" step="width : 10px"></th>
                        <th></th>
                        <th><input type="text" class="form-control search" placeholder="Search Caption" id="caption"></th>
                        <th></th>
                        <th><input type="text" class="form-control search" placeholder="Search Comments" id="comment"></th>
                        
                    </tr>
                   </thead>
                     <tbody>
                   @include('instagram.hashtags.data')
                    </tbody>
                </table>
                
                 {!! $medias->render() !!}
            </div>
        </div>

        
    </div>
@endsection

@section('styles')
    <style>
        .comment-list tr:last-child td {
            color: #e74c3c;
        }
    </style>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
@endsection

@section('scripts')
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        var cid = null;
        $(function(){
            $('.show-details').on('click',function() {
                let id = $(this).attr('data-pid');
                $('.reveal-'+id).slideToggle('slow');
            });

            $('.card-reveal .close').on('click',function(){
                $(this).parent().slideToggle('slow');
            });
        });

        $(document).on('click', '.hide-media', function() {
            let mid = $(this).attr('data-id');
            $.ajax({
                url: '{{ action('HashtagController@flagMedia', '') }}'+'/'+mid,
                success: function() {
                    $("#media_"+mid).hide();
                }
            });
        });

        $(document).on('click', '.load-comment', function() {
            let mediaId = $(this).attr('data-media-id');
            let postCode = $(this).attr('data-post-code');
            $.ajax({
                url: '{{ action('HashtagController@loadComments', '') }}'+'/'+mediaId,
                success: function(response) {
                    let comments = response.comments;
                    if (response.has_more_comments==false) {
                        $('.load-more-'+mediaId).hide();
                    }
                    $('#comments-'+mediaId).html('');
                    comments.forEach(function(comment) {
                        let form = '<form action="{{ action('ReviewController@createFromInstagramHashtag') }}" method="post">@csrf<input type="hidden" name="date" value="'+comment.created_at_time+'"><input type="hidden" name="code" value="'+postCode+'"><input type="hidden" name="post" value="'+response.caption.text+'"><input type="hidden" name="comment" value="'+comment.text+'"><input type="hidden" name="poster" value="'+response.caption.user.username+'"><input type="hidden" name="commenter" value="'+comment.user.username+'"><input type="hidden" name="media_id" value="'+mediaId+'"><button class="btn btn-sm btn-success"><i class="fa fa-check"></i></button></form>';
                        let data = '<tr><td>'+comment.user.username+'</td><td>'+comment.text+'</td><td>'+comment.created_at+'</td><td>'+form+'</td></tr>';
                        $('#comments-'+mediaId).append(data);
                    });
                }
            });
        });

        $(document).on('click', '.expand-row', function() {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                // if ($(this).data('switch') == 0) {
                //   $(this).text($(this).data('details'));
                //   $(this).data('switch', 1);
                // } else {
                //   $(this).text($(this).data('subject'));
                //   $(this).data('switch', 0);
                // }
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

       function loadComments(id){
            $("#commentModal"+id).modal();
       }

        

        $('#hashtags').change(function() {
            let hashtag = $(this).val();
            $('#name').val(hashtag);
        });

        $('.comment-it').click(function() {
            
                let id = $(this).attr('data-id');
                let post_id = $(this).attr('data-post-id');
                let message = $('#textbox_'+id).val();
                let textbox = $('#textbox_'+id);
                let accountId = $('#account_id_'+id).val();
                let accountType = $('#account_id_'+id).data("type");
                let narrative = $('#narrative_'+id).val();
                let selectedusers = $('#selected_user_'+id).val();
                console.log(accountId);
                console.log(accountType);
                if(accountId == 'Select User'){
                    alert('Please Select User to Comment');
                }else{
                    let self = textbox;
                    $(self).attr('disabled', true);
                    
                    $.ajax({
                        url: '{{action('HashtagController@commentOnHashtag')}}',
                        type: 'POST',
                        data: {
                            message: message,
                            account_id: accountId,
                            id : id,
                            post_id : post_id,
                            narrative: narrative,
                            accountType : accountType,
                            hashtag: "{{$hashtag->hashtag}}",
                            _token: '{{ csrf_token() }}'
                        },beforeSend: function() {
                           $("#loading-image").show();
                        },
                        success: function(response) {
                            $("#loading-image").hide();
                            alert(response.status);
                            $(self).removeAttr('disabled');
                            $(self).val('');
                        }
                    });

                }

                
            
        });

 
   
   


         $(document).ready(function() {
        src = "/instagram/hashtag/grid/{{ $hashtag->id }}";
        $(".global").autocomplete({
        source: function(request, response) {
            term = $('#term').val();
            date = $('#date').val();
           
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    term : term,
                    date : date,
                
                },
                beforeSend: function() {
                       $("#loading-image").show();
                },
            
            }).done(function (data) {
                 $("#loading-image").hide();
                console.log(data);
                $("#grid-table tbody").empty().html(data.tbody);
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

//"caption"
// "location
// "comment"

         $(document).ready(function() {
        src = "/instagram/hashtag/grid/{{ $hashtag->id }}";
        $(".search").autocomplete({
        source: function(request, response) {
            username = $('#username').val();
            caption = $('#caption').val();
            comment = $('#comment').val();
         //   location = $('#location').val();
       

           
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    username : username,
                    caption : caption,
                    comment : comment,
               //     location : location,
                
                },
                beforeSend: function() {
                       $("#loading-image").show();
                },
            
            }).done(function (data) {
                 $("#loading-image").hide();
                console.log(data);
                $("#grid-table tbody").empty().html(data.tbody);
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


    function addUserToTextArea(value,id){
        username = '@'+$(value).val();
        
        $("#textbox_"+id).val(function() {
        return this.value +' '+username;
        });
    }
    </script>
@endsection