@extends('layouts.app')


@section('content')
<div class="container-fluid gedf-wrapper">
  <div class="row">



    @if(isset($posts) && !empty($posts))


    @foreach($posts as $post)

    <div class="col-md-8 gedf-main mt-2 ml-auto mr-auto">
      <div class="card gedf-card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex justify-content-between align-items-center">
              <div class="mr-2">
                <img class="rounded-circle" width="45" src="https://picsum.photos/50/50" alt="">
              </div>
              <div class="ml-2">
                @if(isset($post['from']['name']) && !empty($post['from']['name']))
                <div class="h5 m-0">{{$post['from']['name']}}</div>
                @endif

              </div>
            </div>

          </div>

        </div>
        <div class="card-body">
          @if(isset($post['created_time']) && !empty($post['created_time']))
          <div class="text-muted h7 mb-2">
           <i class="fa fa-clock-o"></i>

           {{$post['created_time']->format('Y-m-d H:i:s')}}

         </div>
         @endif

         <a class="card-link" href="#">
          @if(isset($post['name']) && !empty($post['name']))
          <h5 class="card-title">{{$post['name']}}</h5>
          @endif
        </a>
        @if(isset($post['full_picture']) && !empty($post['full_picture']))
        <a href="{{$post['permalink_url']}}" target="_blank">
          <img class="img-responsive" width="inherit" height="inherit"  src="{{$post['full_picture']}}" alt="Not found">
        </a>
        @endif
        @if(isset($post['description']) && !empty($post['description']))
        <p class="card-text">
         {{$post['description']}}
       </p>
       @elseif(isset($post['message']) && !empty($post['message']))
       <p class="card-text">
         {{$post['message']}}
       </p>
       @endif
     </div>

   </div>

 </div>
 @endforeach

 <div class="container text-left mt-4">
   <div class="row">
    @if(isset($posts) && !empty($posts))
    <div class="col-md-6 ml-auto mr-auto">
      <nav aria-label="Page navigation example">
        <ul class="pagination">
         @if(isset($previous))
         <li class="page-item">

           <div class="col-md-4 ">
            <!-- Next -->
            <form method="post" action="{{route('social.get-post.page')}}">
             @csrf
             <input type="hidden" name="previous" value="{{$previous}}">
             <input type="submit" value="Previous" class="btn btn-info">
           </form>
         </div>

       </li>
       @endif
       @if(isset($next))
       <li class="page-item">


        <div class="col-md-4 ml-3">
          <form method="post" action="{{route('social.get-post.page')}}">
            @csrf
            <input type="hidden" name="next" value="{{$next}}">
            <input type="submit" value="Next" class="btn btn-info">
          </form>
        </div>

      </li>
      @endif
    </ul>
  </nav>
</div>


@endif

@endif
</div>
</div>

</div>
</div>




@endsection