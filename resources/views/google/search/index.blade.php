@extends('layouts.app')

@section('title', 'Google Search')


@section('styles')
<style type="text/css">
    .switch {
  position: relative;
  display: inline-block;
  width: 40px;
  height: 24px;
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
  height: 16px;
  width: 16px;
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

.filter-icon {
    font-size: 16px;
}

.btn-trash {
    font-size: 16px;
}

input:checked + .slider:before {
  -webkit-transform: translateX(16px);
  -ms-transform: translateX(16px);
  transform: translateX(16px);
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
@include('partials.flash_messages')
 <div id="myDiv">
      <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 px-0">
                <h2 class="page-heading">Google Search Keywords (<span>{{ $keywords->total() }}</span>) </h2>
            </div>
            <div class="col-md-12">
                @if(Session::has('message'))
                <script>
                    alert("{{Session::get('message')}}")
                </script>
                @endif
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <form action="{{ route('google.search.keyword') }}" method="GET">
                            <div class="form-group">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <input name="term" type="text" class="form-control"
                                               value="{{ isset($term) ? $term : '' }}"
                                               placeholder="Keyword Name">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="form-check d-flex align-items-center pl-0">
                                                <input class="form-check-input mt-0" type="checkbox" name="priority" id="defaultCheck1">
                                                <label class="form-check-label pl-4 ml-2 font-weight-normal" for="defaultCheck1">
                                                    Priority
                                                </label>
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-secondary ml-4"><i class="fa fa-filter filter-icon" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    <div class="col-md-6">

                        <form method="post" action="{{ action([\App\Http\Controllers\GoogleSearchController::class, 'store']) }}">
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
                                        <button class="btn-block btn btn-primary">Add Keyword</button>
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
                        <th><a href="/google/search/keyword{{ ($queryString) ? '?'.$queryString : '?' }}sortby=keyword{{ ($orderBy == 'DESC') ? '&orderby=ASC' : '' }}">Keyword</a></th>
                        <th>Priority</th>
                        <th>Run Scraper</th>
                        <th>Actions</th>
                    </tr>
                    @foreach($keywords as $key=>$keyword)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>
                            {{ $keyword->hashtag }}
                        </td>
                        <td>
                            <label class="switch mb-0">
                                @if($keyword->priority == 1)
                                <input type="checkbox" checked class="checkbox" value="{{ $keyword->id }}">
                                @else
                                <input type="checkbox" class="checkbox" value="{{ $keyword->id }}">
                                @endif
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <button class="btn py-0 btn-default " id="runScrapper_{{ $keyword->id }}" onclick="callScraper({{ $keyword->id }})">Run Scraper For {{ $keyword->hashtag }}</button>
                        </td>
                        <td>
                            <form method="post" action="{{ action([\App\Http\Controllers\GoogleSearchController::class, 'destroy'], $keyword->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-default btn-trash btn-image border-0 btn-sm">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </table>
                {!! $keywords->appends(Request::except('page'))->links() !!}
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection

@section('scripts')
    <script>
    $(document).ready(function() {
        $(".checkbox").change(function() {
            id = $(this).val();
               
            if(this.checked) {
               $.ajax({
                    type: 'GET',
                    url: '{{ route('google.search.keyword.priority') }}',
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
                    url: '{{ route('google.search.keyword.priority') }}',
                    data: {
                        id:id,
                        type: 0
                    },
                        }).done(response => {
                         alert('Keyword Removed Priority');    
                    }); 
            }
        });
    });

    function callScraper(id){
        var buttonCaption = $('#runScrapper_'+id).html();
        $('#runScrapper_'+id).html('Initiating...');
        $('#runScrapper_'+id).prop('disabled', true);
        //ajax call coming here...
        $.ajax({
            url: "{{ route('google.search.keyword.scrap') }}",
            type: 'GET',
            data: {
                id: id,
                _token: "{{ csrf_token() }}"
            },            
            success: function(data) {
                $('#runScrapper_'+id).prop('disabled', false);
                $('#runScrapper_'+id).html(buttonCaption);
                alert('Scapper initiated successfully');
            }
        });
    }
    </script>
@endsection