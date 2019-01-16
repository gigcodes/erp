@extends('layouts.app')


@section('content')
<div class="container-fluid gedf-wrapper">
  <div class="row">



    @if(isset($posts) && !empty($posts))


    @foreach($posts as $key=>$post)
                <div class="col-md-6">
                  <div class="card">
                      <div class="card-image">
                          <img class="img-responsive" src="{!! $post['full_picture'] ?? 'http://lorempixel.com/555/300/black' !!}">

                      </div><!-- card image -->

                      <div class="card-content">
                            <span class="card-title">
                                <span>
                                    <i class="fa fa-heart text-danger"></i> {{ $post['likes']['summary']['total_count'] }}
                                </span>
                                <span class="ml-4 cp">
                                    <i class="fa fa-comment text-info show-details" data-pid="{{ $key }}"></i> {{ $post['comments']['summary']['total_count'] }}
                                </span>
                            </span>
                          <button type="button" class="btn btn-custom pull-right show-details" data-pid="{{ $key }}" aria-label="Left Align">
                              <i class="fa fa-ellipsis-v"></i>
                          </button>
                      </div><!-- card content -->
                      <div class="card-action">
                          <span class="text-muted" title="{{ isset($post['created_time']) ? $post['created_time']->format('Y-m-d H:i:s') : 'N/A' }}">
                              <strong>
                                  {{ isset($post['created_time']) ? \Carbon\Carbon::createFromTimestamp(strtotime($post['created_time']->format('Y-m-d H:i:s')))->diffForHumans() : 'N/A' }}
                              </strong>
                          </span>
                          <p>
                              {!! $post['message'] ? preg_replace('/(?:^|\s)#(\w+)/', ' <a class="text-info" href="https://www.facebook.com/hashtag/$1">#$1</a>', $post['message']) : '' !!}
                          </p>
                      </div><!-- card actions -->
                      <div class="card-reveal reveal-{{ $key }}">
                          <span class="card-title">Comments ({{ $post['comments']['summary']['total_count'] }})</span> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                          @if ($post['comments']['items'])
                              @foreach($post['comments']['items'] as $item)
                                  <p class="comment text-justify" data-cid="{{ $item['id'] }}">
                                      <span><button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>
                                      <span class="text-info"></span>
                                      <p>{!! $item['message'] !!}</p>
                                  </p>
                              @endforeach
                          @else
                              <div class="alert alert-warning alert-margin">
                                  <strong>There are no comments on this post!</strong>
                              </div>
                          @endif
                          <div class="form-group">
                              <input type="text" class="form-control" placeholder="Leave a comment...">
                          </div>
                      </div><!-- card reveal -->
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

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection

@section('scripts')
    <script>
        $(function(){

            $('.show-details').on('click',function(){
                var id = $(this).attr('data-pid');
                console.log(id);
                $('.reveal-'+id).slideToggle('slow');
            });

            $('.card-reveal .close').on('click',function(){
                $(this).parent().slideToggle('slow');
            });
        });

        function getComments(postId) {

        }


    </script>
@endsection