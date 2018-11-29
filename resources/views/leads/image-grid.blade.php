@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2>Leads Image Grid</h2>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    <div class="row mt-6">
      @foreach ($leads as $lead)
        {{-- {{ dd($lead) }} --}}
        <div class="col-md-3 col-xs-6 text-center">
          <a href="{{ route('leads.show', $lead['id']) }}">
            <img src="{{ $lead['image'] }}" class="img-responsive grid-image" alt="" />
            <p>Status : {{ $lead['status'] }}</p>
            <p>Rating : {{ $lead['rating'] }}</p>
          </a>
        </div>
      @endforeach
    </div>

    {!! $leads->links() !!}

@endsection
