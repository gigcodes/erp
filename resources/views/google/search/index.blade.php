@extends('layouts.app')

@section('title', 'Google Search')


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
 #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
</style>
@endsection
@section('content')
 <div id="myDiv">
      <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Google Search Keywords</h2>
        </div>
        <div class="col-md-12">
            @if(Session::has('message'))
                <script>
                    alert("{{Session::get('message')}}")
                </script>
            @endif
            <div class="row">
                <div class="col-md-6">
                    
                    <form action="{{ route('google.search') }}" method="GET">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5">
                                    <input name="term" type="text" class="form-control"
                                      value="{{ isset($term) ? $term : '' }}"
                                      placeholder="Keyword Name">
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
                     
               <form method="post" action="{{ action('GoogleSearchController@store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Keyword</label>
                            <input type="text" name="name" id="name" placeholder="sololuxuryindia" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Add?</label>
                            <button class="btn-block btn btn-default">Add Keyword</button>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="platform_id" value="2">
            </form>

            
                </div>
            </div>
            
           
        </div>
        <div class="col-md-12">
            <table class="table-striped table-bordered table table-sm">
                <tr>
                    <th>S.N</th>
                    <th><a href="/google/search{{ ($queryString) ? '?'.$queryString : '?' }}sortby=keyword{{ ($orderBy == 'DESC') ? '&orderby=ASC' : '' }}">Keyword</a></th>
                    <th>Actions</th>
                    <th>Priority</th>
                </tr>
                @foreach($keywords as $key=>$keyword)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>
                            {{ $keyword->hashtag }}
                        </td>
                        <td>
                            <form method="post" action="{{ action('GoogleSearchController@destroy', $keyword->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-default btn-image btn-sm">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                          <label class="switch">
                              @if($keyword->priority == 1)
                              <input type="checkbox" checked class="checkbox" value="{{ $keyword->id }}">
                              @else
                              <input type="checkbox" class="checkbox" value="{{ $keyword->id }}">
                              @endif
                              <span class="slider round"></span>
                            </label>
                            <!-- <button onclick="runCommand({{ $keyword->id }})">Run Command</button> -->
                        </td>
                    </tr>
                @endforeach
            </table>
             {!! $keywords->appends(Request::except('page'))->links() !!}
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection

@section('scripts')
    <script>
        $(".checkbox").change(function() {
            id = $(this).val();
               
            if(this.checked) {
               $.ajax({
                    type: 'GET',
                    url: '{{ route('google.keyword.priority') }}',
                    data: {
                        id:id,
                        type: 1
                    },success: function (data) {
                      console.log(data);
                        if(data.status == 'error'){
                           alert('Priority Limit Exceded'); 
                           location.reload(true);
                           
                        }else{
                           alert('Keyword Priority Added');  

                        }
                      
                    },
                    error: function (data) {
                       alert('Priority Limit Exceded');
                    }
                        });
            }else{
                 $.ajax({
                    type: 'GET',
                    url: '{{ route('google.keyword.priority') }}',
                    data: {
                        id:id,
                        type: 0
                    },
                        }).done(response => {
                         alert('Keyword Removed Priority');    
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
                    },beforeSend: function() {
                       $("#loading-image").show();
                    },success: function (data) {
                      $("#loading-image").hide();
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