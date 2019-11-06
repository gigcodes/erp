@extends('layouts.app')

@section('styles')
<style type="text/css">
    .switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Instagram HashTags</h2>
        </div>
        <div class="col-md-12">
            @if(Session::has('message'))
                <script>
                    alert("{{Session::get('message')}}")
                </script>
            @endif
            <div class="row">
                <div class="col-md-6">
                    
                            <form action="{{ route('hashtag.index') }}" method="GET">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <input name="term" type="text" class="form-control"
                                                   value="{{ isset($term) ? $term : '' }}"
                                                   placeholder="Tag Name">

                                        </div>
                                        <div class="col-md-1">
                                            <input type="checkbox" name="priority">Priority  
                                        </div>
                                       <div class="col-md-6">
                                        <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                                        </div>

                                    </div>
                                </div>
                            </form>
                    
                </div>
                <div class="col-md-6">
                     
               <form method="post" action="{{ action('HashtagController@store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Hashtag (without # symbol)</label>
                            <input type="text" name="name" id="name" placeholder="sololuxuryindia (without hash)" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Add?</label>
                            <button class="btn-block btn btn-default">Add Hashtag</button>
                        </div>
                    </div>
                </div>
            </form>

            
                </div>
            </div>
            
           
        </div>
        <div class="col-md-12">
            <table class="table-striped table-bordered table table-sm">
                <tr>
                    <th>S.N</th>
                    <th>Tag Name</th>
                    <th>Count</th>
                    <th>Rating</th>
                    <th>Actions</th>
                    <th>Priority</th>
                </tr>
                @foreach($hashtags as $key=>$hashtag)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>
                            <a href="{{ action('HashtagController@showGrid',$hashtag->id) }}">
                                {{ $hashtag->hashtag }}
                            </a>
                        </td>
                        <td>{{$hashtag->post_count}}</td>
                        <td>{{ $hashtag->rating }}</td>
                        <td>
                            <form method="post" action="{{ action('HashtagController@destroy', $hashtag->id) }}">
                                <a class="btn btn-default btn-image" href="{{ action('HashtagController@showGrid', $hashtag->id) }}">
                                    <img src="{{ asset('images/view.png') }}" alt="">
                                </a>
                                <a class="btn btn-default btn-image" href="{{ action('HashtagController@edit', $hashtag->hashtag) }}">
                                    <i class="fa fa-info"></i> Relavent Hashtags
                                </a>
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-default btn-image btn-sm">
                                    <i class="fa fa-trash"></i>
                                </button>
                                
                            </form>
                        </td>
                        <td>
                          <label class="switch">
                                  @if($hashtag->priority == 1)
                                  <input type="checkbox" checked class="checkbox" value="{{ $hashtag->id }}">
                                  @else
                                  <input type="checkbox" class="checkbox" value="{{ $hashtag->id }}">
                                  @endif
                                  <span class="slider round"></span>
                                </label>
                                <button onclick="runCommand({{ $hashtag->id }})">Run Command</button>
                        </td>
                    </tr>
                @endforeach
            </table>
             {!! $hashtags->appends(Request::except('page'))->links() !!}
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection

@section('scripts')
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

        $(".checkbox").change(function() {
            id = $(this).val();
               
            if(this.checked) {
               $.ajax({
                    type: 'GET',
                    url: '{{ route('hashtag.priority') }}',
                    data: {
                        id:id,
                        type: 1
                    },success: function (data) {
                      console.log(data);
                        if(data.status == 'error'){
                           alert('Priority Limit Exceded'); 
                           location.reload(true);
                           
                        }else{
                           alert('Hashtag Priority Added');  

                        }
                      
                    },
                    error: function (data) {
                       alert('Priority Limit Exceded');
                    }
                        });
            }else{
                 $.ajax({
                    type: 'GET',
                    url: '{{ route('hashtag.priority') }}',
                    data: {
                        id:id,
                        type: 0
                    },
                        }).done(response => {
                         alert('Hashtag Removed Priority');    
                    }); 
            }
        });

        function runCommand(id) {
             $.ajax({
                    type: 'POST',
                    url: '{{ route('hashtag.command') }}',
                    data: {
                        id:id,
                         _token: "{{ csrf_token() }}"
                    },success: function (data) {
                     
                        if(data.status == 'error'){
                           alert('Something went wrong'); 
                           location.reload(true);
                           
                        }else{
                           alert('Hashtag Added To Fetch Post');  

                        }
                      
                    },
                    error: function (data) {
                       alert('Something went wrong');
                    }
                        });
        }
    </script>
@endsection