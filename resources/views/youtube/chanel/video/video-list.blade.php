@extends('layouts.app')

@section('title', 'Video List')

@section('styles')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
@endsection
@section('content')

   <div class="videoList">
    <h2 class="text-center">Video List</h2>
    <br>
    @foreach($videoData as $value)
    <div class="col-md-4">
    <div class="inner" style="padding:10px 0px 0px 0px">
        <iframe  width="400" height="300" src="{{ $value['link'] }}" allowfullscreen="allowfullscreen" allowtransparency="" allow="autoplay"></iframe>
    </div>
    <button  class="btn btn-primary"><a href="{{ route('commentList', ['websiteId' => $websiteId,'videoId' => $value['media_id']]) }}" style="color:#fff" >View Comments</a></button></button>
    <br>
    </div>

   @endforeach 
   </div>

@endsection

<script>
    <!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</script>