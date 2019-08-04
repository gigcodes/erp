@extends('layouts.app')

@section('title', 'Backlinking Data')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Backlinking Data</h2>
        </div>
    </div>
    <form action="{{ route('backLinkFilteredResults') }}" method="GET" class="form-inline align-items-start mb-5 float-right">
      <div class="form-group mr-3 mb-4">
        <select name="title" class="form-control" placeholder="Titles">
          @foreach ($titles as $title)
            <option value="{{$title}}" {{!empty($_GET['title']) ? $_GET['title'] : ''}}>{{$title}}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
    </form>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th rowspan="2">Sr. No</th>
            <th rowspan="2" class="text-center">Title</th>
            <th rowspan="2" class="text-left">Description</th>
            <th rowspan="2" class="text-center">URL</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($details as $key => $detail)
                <tr>
                    <td class="readmore">@php echo htmlspecialchars_decode(stripslashes(str_limit($detail->id, 50, '<a href="javascript:void(0)">...</a>'))); @endphp</td>
                    <td class="readmore">@php echo htmlspecialchars_decode(stripslashes(str_limit($detail->title, 50, '...'))); @endphp
                        @if (strlen(strip_tags($detail->title)) > 50)
                        <div>
                            <div class="panel-group">
                              <div class="panel panel-default">
                                <div class="panel-heading">
                                  <h4 class="panel-title">
                                  <a data-toggle="collapse" href="#collapse_title-{{$key}}" class="collapsed" aria-expanded="false">Read More</a>
                                  </h4>
                                </div>
                              <div id="collapse_title-{{$key}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                  <div class="panel-body">
                                    <div class="messageList" id="message_list_310">
                                        {{$detail->title}}     
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        @endif
                    </td>
                    <td class="readmore">@php echo htmlspecialchars_decode(stripslashes(str_limit($detail->description, 50, '<a href="javascript:void(0)">...</a>'))); @endphp
                      @if (strlen(strip_tags($detail->description)) > 50)
                        <div>
                            <div class="panel-group">
                              <div class="panel panel-default">
                                <div class="panel-heading">
                                  <h4 class="panel-title">
                                    <a data-toggle="collapse" href="#collapse_desc{{$key}}" class="collapsed" aria-expanded="false">Read More</a>
                                  </h4>
                                </div>
                                <div id="collapse_desc{{$key}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                  <div class="panel-body">
                                    <div class="messageList" id="message_list_310">
                                        {{$detail->description}}     
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        @endif
                    </td>
                    <td class="readmore">@php echo htmlspecialchars_decode(stripslashes(str_limit($detail->url, 50, '<a href="javascript:void(0)">...</a>'))); @endphp
                      @if (strlen(strip_tags($detail->url)) > 50)
                        <div>
                            <div class="panel-group">
                              <div class="panel panel-default">
                                <div class="panel-heading">
                                  <h4 class="panel-title">
                                    <a data-toggle="collapse" href="#collapse_url-{{$key}}" class="collapsed" aria-expanded="false">Read More</a>
                                  </h4>
                                </div>
                                <div id="collapse_url-{{$key}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                  <div class="panel-body">
                                    <div class="messageList" id="message_list_310">
                                        {{$detail->url}}     
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        @endif
                    </td>
                </tr>    
            @endforeach
        </tbody>
      </table>
      <div class="text-center">
          <div class="text-center">
              {!! $details->links() !!}
          </div>
      </div>
    </div>
@endsection
