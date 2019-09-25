@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">{{ $sop->name }} - SOP</h2>
        </div>
         @if(auth()->user()->isAdmin())
            <?php $aayo = true; ?>
            <div class="col-md-12">
                <form method="post" action="{{ action('ProductController@saveSOP') }}">
                    @csrf
                    <input type="hidden" name="type" value="{{$sop->name}}">
                    <textarea name="content" id="content" cols="30" rows="10">{!! $sop->content !!}</textarea>
                    <button class="btn btn-secondary">Save</button>
                </form>
            </div>
        @endif
        @if(!isset($aayo))
            <div class="col-md-12">
                {!! $sop->content !!}
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/4.11.4/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'content' );
    </script>
@endsection