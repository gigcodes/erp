@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Quick Replies List</h2>
        <div class="pull-left">
            <div class="row">
                <div class="col-md-12 ml-5">            
                    <form action="{{ route('reply.replyList') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-6 pd-sm">
                                {{ Form::select("store_website_id", ["" => "-- Select Website --"] + \App\StoreWebsite::pluck('website','id')->toArray(),request('store_website_id'),["class" => "form-control"]) }}
                            </div>
                            <div class="col-md-5 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control" value="{{ request()->get('keyword') }}">
                            </div>
                            

                            <div class="col-md-1 pd-sm">
                                 <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content ">
    <!-- Pending task div start -->
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;"> 
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th width="2%">ID</th>
                            <th width="2%">Store website</th>
                            <th width="25%">Category</th>
                            <th width="10%">Reply</th>
                            <th width="5%">Model</th>
                        </tr>
                        @foreach ($replies as $key => $reply)
                            <tr>
                                <td id="reply_id">{{ $reply->id }}</td>
                                <td id="reply-store-website">{{ $reply->website }}</td>
                                <td id="reply_category_name">{{ $reply->parentList() }} > {{ $reply->category_name }}</td>
                                <td id="reply_text">{{ $reply->reply }}</td>
                                <td id="reply_model">{{ $reply->model }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                    {!! $replies->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>                        
<script type="text/javascript">

</script>
@endsection