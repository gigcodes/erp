@extends('layouts.app')

@section('content')
    <div class="col-md-12">
        <h2 class="page-heading">Bulk Customer Replies</h2>
    </div>
    <div class="col-md-12">
        @if(Session::has('message'))
            <div class="alert alert-info">
                {{ Session::get('message') }}
            </div>
        @endif
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>Keywords, Phrases & Sentences</strong>
            </div>
            <div class="panel-body">
                <form method="post" action="{{ action('BulkCustomerRepliesController@storeKeyword') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <input type="text" name="keyword" id="keyword" placeholder="Keyword, phrase or sentence..." class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button class="btn btn-secondary btn-block">Add New</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="alert alert-warning">
                    <strong>Note: Click any tag below, and it will show the customer with the keywords used.</strong>
                </div>
                <div>
                    <strong>Manually Added</strong><br>
                    @foreach($keywords as $keyword)
                        <a href="{{ action('BulkCustomerRepliesController@index', ['keyword_filter' => $keyword->value]) }}" style="font-size: 14px;" class="label label-default">{{$keyword->value}}</a>
                    @endforeach
                </div>
                <div class="mt-2">
                    <strong>Auto Generated</strong><br>
                    @foreach($autoKeywords as $keyword)
                        <span class="label label-default">{{$keyword->value}}({{$keyword->count}})</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        @if($searchedKeyword)
            @if($searchedKeyword->customers)
                <form action="{{ action('BulkCustomerRepliesController@sendMessagesByKeyword') }}" method="post">
                    @csrf
                    <table class="table table-striped table-bordered">
                        <tr>
                            <td>Pick?</td>
                            <td>S.N</td>
                            <td>Customer</td>
                            <td>Recent Messages</td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="row">
                                    <div class="col-md-11">
                                        <textarea name="message" id="message" rows="1" class="form-control" placeholder="Common message.."></textarea>
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-secondary btn-block">Send</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @foreach($searchedKeyword->customers as $key=>$customer)
                            <tr>
                                <td><input type="checkbox" name="customers[]" value="{{ $customer->id }}"></td>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>
                                    @foreach($customer->messageHistory as $message)
                                        <li>{{ $message->message }}</li>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </form>
            @else
            @endif
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://rawgit.com/jackmoore/autosize/master/dist/autosize.min.js"></script>
    <script>
        autosize(document.getElementById("message"));
    </script>
@endsection