@extends('layouts.app')

@section('title', __('New post'))

@section('content')
<style>
    .imagecheck-image{
        height: 100px !important;
        width: 100px !important
    }
    .light-blue.lighten-2 {
        background-color: #4fc3f7 !important;
        margin: 0px 4px;
    }   
    .light-blue.lighten-2 a {
        color: #fff;
        
    }
    div#auto {
        padding: 15px 0px;
    }
    #loading-image {
        position: fixed;
        top: 50%;
        left: 50%;
        margin: -50px 0px 0px -50px;
        z-index: 60;
    }
   
</style>
<link rel="stylesheet" href="{{ asset('/css/instagram.css') }}?v={{ config('pilot.version') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropme@latest/dist/cropme.min.css">   
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

@includeWhen($accounts->count() == 0, 'partials.no-accounts')


<div class = "row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Instagram Posts</h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="panel mt-3 panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                       Posts
                    </h4>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Account</th>
                            <th>Type</th>
                            <th>IG</th>
                            <th>Caption</th>
                            <th>Action</th>
                        </tr>
                        @foreach ($posts as $post)
                            @php $accountName = ''; @endphp
                            @foreach($accounts as $account)
                                @if($account->id == $post->account_id)
                                    @php
                                    $accountName = $account->first_name;
                                    break;
                                    @endphp
                                @endif
                            @endforeach
                            <tr>
                                <td>{{$accountName}}
                                    <br/><a href="/attachImages/customer/961/1?return_url=/erp-customer?type=last_received&do_not_disturb=0&page=2">Attachments</a>
                                </td>
                                <td>{{$post->type}}</td>
                                <td>{{$post->ig}}</td>
                                <td>{{$post->caption}}</td>
                                <td>
                                    <button type="button" class="btn" data-toggle="modal" data-target="#add-vendor-info-modal" title="" data-id="{{$post->id}}"><i class="fa fa-eye" aria-hidden="true"></i></button>
                                    <button type="button" class="btn" data-toggle="modal" data-target="#add-insta-feed-model" title="" data-id="{{$post->id}}"><i class="fa fa-info-circle" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@include('instagram.partials.publish-post')

<div id="add-insta-feed-model" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <body class="post-create">
                <div class="modal-header">
                </div>
                <div class="modal-body">
                    <div class="post-create">
                        <div class="col-md-12">
                            <div class="card preview-story d-none"></div>
                            <div class="card preview-timeline">
                                <div class="pt-5 pb-2 text-center">
                                    <img src="{{ asset('/images/ig-logo.png') }}" alt="Instagram">
                                </div>
                                <div class="p-3 d-flex align-items-center px-2">
                                    <div class="avatar avatar-md mr-3"></div>
                                    <div>
                                        <div class="preview-username active"></div>
                                        <small class="d-block text-muted preview-location active"></small>
                                    </div>
                                </div>
                                <div class="image-preview">
                                    <div id="carousel" class="carousel slide">
                                        <ol class="carousel-indicators"></ol>
                                        <div class="carousel-inner"></div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="preview-caption active">
                                        <span></span>
                                        <span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </body>
        </div>
    </div>
</div>

@endsection
    
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="{{ asset('/js/instagram.js') }}?v={{ config('pilot.version') }}" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/cropme@latest/dist/cropme.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>
<script src="{{ asset('js/bootstrap-notify.js') }}"></script>

<script>
$(document).ready(function(){
    //alert("Hello");
    /*$('.add-vendor-info').on('click', function () {
        alert("Add");
        $("#add-vendor-info-modal").modal("show");
    });*/

    $(".caption-text .emojionearea-editor").keyup(function(){
        console.log($(this).val());
    });


    var el = $("#caption_ig").emojioneArea();
    el[0].emojioneArea.on("keyup", function(val, event) {

        var text = val[0].innerText;

        var n = text.split(" ");
        var lastWord = n[n.length - 1];

        //Checking if string starts with hash to send ajax
        var startWith = lastWord.charAt(0);
        //console.log("Starts With: "+startWith);
        //console.log("last word: "+lastWord);
        if(startWith=="#")
        {
            console.log("last word: "+lastWord);
            var wordToSearch = lastWord.substring(1);
            if(wordToSearch)
            {
                $.ajax({
                    type: "get",
                    url: "/instagram/get/hashtag/"+wordToSearch,
                    async: true,
                    dataType: 'json',
                    beforeSend: function () {
                        $(".emojionearea-editor").attr('contenteditable','false');
                        $("#loading-image").show();
                    },
                    success: function(data){
                        $("#loading-image").hide();
                        $(".emojionearea-editor").attr('contenteditable','true');
                        //console.log(data.length);
                        //console.log(data[2]);
                        $('#auto').html('');

                        for(x = 0; x < data.length; x++)
                        {
                            $('#auto').append("<div class=chip light-blue lighten-2 white-text waves-effect'><a href='#' data-hashtag='"+data[x]+"' data-caption ='"+text+"' >"+data[x]+"</a></div>"); //Fills the #auto div with the options
                        }
                    }
                });
            }else{
                console.log("No Hashtag work entered");
            }
        }else{
            console.log("Typing normal caption");
        }


        $(document).on('click',"#auto a", function(){
            var hashtag= $(this).data('hashtag');
            var caption = $(this).data('caption');
            
            var lastIndex = caption.lastIndexOf(" ");
            var stringWithoutLastHashtag = caption.substring(0, lastIndex);

            //var stringWithoutLastHashtag = caption.substring(0, caption.lastIndexOf(" "));
            var finalString = stringWithoutLastHashtag.concat(" #"+hashtag);
            console.log("\n "+finalString);
            $(".emojionearea-editor").html(finalString);
            $('#auto').html('');
        });
    });  
    
});


</script>
@endsection
