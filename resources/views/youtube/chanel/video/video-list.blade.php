@extends('layouts.app')

@section('title', 'Video List')

@section('styles')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
@endsection
@section('content')
   {{--  <div class="row">
    <div class="col-md-12 p-0">
    <h4 class="page-heading">Youtube Chanel Count (<span id="ads_account_count"></span>)</h4>
    </div>
    </div>
    <div class="pull-left">
        <div class="form-group">
            <div class="row"> 
                
                <div class="col-md-5">
                    <input name="accountname" type="text" class="form-control" value="{{ isset($accountname) ? $accountname : '' }}" placeholder="Channel Name" id="accountname">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn mt-0 btn-image" onclick="submitSearch()"><img src="/images/filter.png" /></button>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn mt-0 btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png" /></button>
                </div>
            </div>
        </div>
    </div>  --}}
   <div class="videoList">
    
    <table class="table table-bordered">
    <thead>
    <h2 class="text-center">Video List</h2>
    <br>
      <tr>
        
        <th>Video</th>
        <th>View Count</th>
        <th>Title</th>
        <th>Description</th>
        <th>Like Count</th>
        <th>Dislike Count</th>
        <th>CommentCount </th>
        <th>Create Date & Time</th>
        <th>Action</th>
      </tr>
    </thead>
    @if(count($videoList) > 0)
    <tbody>
    @foreach($videoList as $value)
      <tr>
        <td><iframe  width="200" height="100" src="{{ $value['link'] }}" allowfullscreen="allowfullscreen" allowtransparency="" allow="autoplay"></iframe></td>
        <td>{{$value->view_count}}</td>
        <td>{{$value->title}}</td>
        <td>{{$value->description}}</td>
        <td>{{$value->like_count}}</td>
        <td>{{$value->dislike_count}}</td>
        <td>{{$value->comment_count}}</td>
        <td>{{$value->create_time}}</td>
        <td><button  class="btn btn-primary"><a href="/youtube/comment-list/{{$value['media_id']}}" style="color:#fff" >View Comments</a></button></button></td>
      </tr>
     
    @endforeach

  
    </tbody>
      
    @else
   <h4 class="Text-danger text-center jumbotron">Video Not Available...</h4>
   @endif
  </table>
  {{ $videoList->links() }}
  



@endsection

<script>
    <!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</script>