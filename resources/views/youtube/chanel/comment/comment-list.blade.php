@extends('layouts.app')

@section('title', 'Comment List')

@section('styles')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
  

.card {
    
    border: none;
    box-shadow: 5px 6px 6px 2px #e9ecef;
    border-radius: 4px;
}


.dots{

    height: 4px;
  width: 4px;
  margin-bottom: 2px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
}

.badge{

        padding: 7px;
        padding-right: 9px;
    padding-left: 16px;
    box-shadow: 5px 6px 6px 2px #e9ecef;
}

.user-img{

    margin-top: 4px;
}

.check-icon{

    font-size: 17px;
    color: #c3bfbf;
    top: 1px;
    position: relative;
    margin-left: 3px;
}

.form-check-input{
    margin-top: 6px;
    margin-left: -24px !important;
    cursor: pointer;
}


.form-check-input:focus{
    box-shadow: none;
}


.icons i{

    margin-left: 8px;
}
.reply{

    margin-left: 12px;
}

.reply small{

    color: #b7b4b4;

}


.reply small:hover{

    color: green;
    cursor: pointer;

}
</style>
@endsection
@section('content')

<div class="container mt-5">

            <div class="row  d-flex justify-content-center">

                <div class="col-md-8">
                @if(count($comments) > 0)
                    @foreach($comments as $value)
                                    {{--  <div class="headings d-flex justify-content-between align-items-center mb-3">
                                        <h5>Unread comments(6)</h5>

                                        <div class="buttons">

                                            <span class="badge bg-white d-flex flex-row align-items-center">
                                                <span class="text-primary">Comments "ON"</span>
                                                <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked>
                                                
                                                </div>
                                            </span>
                                            
                                        </div>
                                        
                                    </div>  --}}



                                        <div class="card p-3">

                                            <div class="d-flex justify-content-between align-items-center">

                                        <div class="user d-flex flex-row align-items-center">

                                            {{--  <img src="https://i.imgur.com/hczKIze.jpg" width="30" class="user-img rounded-circle mr-2">  --}}
                                            <span><small class="font-weight-bold text-primary">{{$value['authorDisplayName']}}</small> <small class="font-weight-bold">{{$value['textOriginal']}}</small></span>
                                            
                                        </div>


                                        <small>{{date('d-m-Y H:i:s a', strtotime($value['publishedAt']))}}</small>

                                        </div>

                        {{--  
                                            <div class="action d-flex justify-content-between mt-2 align-items-center">

                                                <div class="reply px-4">
                                                    <small>Remove</small>
                                                    <span class="dots"></span>
                                                    <small>Reply</small>
                                                    <span class="dots"></span>
                                                    <small>Translate</small>
                                                
                                                </div>

                                                <div class="icons align-items-center">

                                                    <i class="fa fa-star text-warning"></i>
                                                    <i class="fa fa-check-circle-o check-icon"></i>
                                                    
                                                </div>
                                                
                                            </div>  --}}


                        
                    </div>
                        @endforeach

                  @else
                    <h4 class="Text-danger text-center jumbotron">No Comment..</h4>
                  @endif      
                </div>
                
            </div>
            
        </div>

@endsection

<script>
    <!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</script>