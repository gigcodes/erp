@extends('layouts.app')

@section('title', 'Backlink Checker')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Backlink Checker</h2>
        </div>
    </div>
    <form action="{{ route('filteredResults') }}" method="GET" class="form-inline align-items-start mb-5 float-right">
      <div class="form-group mr-3 mb-4">
        <select name="domain" class="form-control" placeholder="Domains">
          <option value="Domain" disabled selected></option>
          @foreach ($domains as $domain)
            <option value="{{$domain}}" {{!empty($_GET['domain']) ? $_GET['domain'] : ''}}>{{$domain}}</option>
          @endforeach
        </select>
        {{-- {!! Form::select('domain', $domains, !empty($_GET['domain']) ? $_GET['domain'] : '', ['placeholder'=> 'Domains']) !!} --}}
      </div>
      <div class="form-group mr-3 mb-4">
          <select name="rank" class="form-control" placeholder="Rank">
            <option value="Rank" disabled></option>
              @foreach ($rankings as $rank)
                <option value="{{$rank}}" {{!empty($_GET['rank']) ? $_GET['rank'] : ''}}>{{$rank}}</option>
              @endforeach
          </select>
        {{-- {!! Form::select('ranking', $rankings, !empty($_GET['ranking']) ? $_GET['ranking'] : '', ['placeholder'=> 'Rankings']) !!} --}}
      </div>
      <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
    </form>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th rowspan="2">Domain</th>
            <th rowspan="2" class="text-center">ID</th>
            <th rowspan="2" class="text-left">Link</th>
            <th rowspan="2" class="text-center">Link Type</th>
            <th rowspan="2" class="text-center">Review Numbers</th>
            <th rowspan="2" class="text-center">Rank</th>
            <th rowspan="2" class="text-center">Rating</th>
            <th rowspan="2" class="text-center">Serp ID</th>
            <th rowspan="2" class="text-center">Snippet</th>
            <th rowspan="2" class="text-center">Title</th>
            <th rowspan="2"  class="text-center">Visible Link</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($details as $detail)
                <tr>
                    <td class="readmore">@php echo htmlspecialchars_decode(stripslashes(str_limit($detail->domains, 10, '<a href="javascript:void(0)">...</a>'))); @endphp</td>
                    <td class="readmore">@php echo htmlspecialchars_decode(stripslashes(str_limit($detail->id, 10, '<a href="javascript:void(0)">...</a>'))); @endphp</td>
                    <td class="readmore">@php echo htmlspecialchars_decode(stripslashes(str_limit($detail->link, 10, '<a href="javascript:void(0)">...</a>'))); @endphp</td>
                    <td class="readmore">@php echo htmlspecialchars_decode(stripslashes(str_limit($detail->link_type, 10, '<a href="javascript:void(0)">...</a>'))); @endphp</td>
                    <td class="readmore">@php echo htmlspecialchars_decode(stripslashes(str_limit($detail->num_reviews, 10, '<a href="javascript:void(0)">...</a>'))); @endphp</td>
                    <td class="readmore">@php echo htmlspecialchars_decode(stripslashes(str_limit($detail->rank, 10, '<a href="javascript:void(0)">...</a>'))); @endphp</td>
                    <td class="readmore">@php echo htmlspecialchars_decode(stripslashes(str_limit($detail->rating, 10, '<a href="javascript:void(0)">...</a>'))); @endphp</td>
                    <td class="readmore">@php echo htmlspecialchars_decode(stripslashes(str_limit($detail->serp_id, 10, '<a href="javascript:void(0)">...</a>'))); @endphp</td>
                    <td class="readmore">@php echo htmlspecialchars_decode(stripslashes(str_limit($detail->snippet, 10, '<a href="javascript:void(0)">...</a>'))); @endphp</td>
                    <td class="readmore">@php echo htmlspecialchars_decode(stripslashes(str_limit($detail->title, 10, '<a href="javascript:void(0)">...</a>'))); @endphp</td>
                    <td class="readmore">@php echo htmlspecialchars_decode(stripslashes(str_limit($detail->visible_link, 10, '<a href="javascript:void(0)">...</a>'))); @endphp</td>
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
