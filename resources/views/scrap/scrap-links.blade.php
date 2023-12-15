@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Scrap Links</h2>
        </div>

        <div class="col-md-12">
            <form class="form-inline" method="GET">
                <div class="form-group ml-3">
                    <?php echo Form::text("search", request()->get("search", ""), ["class" => "form-control", "placeholder" => "Enter keyword for search"]); ?>
                </div>
                <div class="form-group ml-3">
                    <select class="form-control" name="status">
                        <option value="">Select Status</option>
                        <option value="in stock" {{request()->get('status') == 'in stock' ? 'selected' : ''}}>in stock</option>
                        <option value="out of stock" {{request()->get('status') == 'out of stock' ? 'selected' : ''}}>out of stock</option>
                        <option value="new" {{request()->get('status') == 'new' ? 'selected' : ''}}>new</option>
                    </select>
                </div>

                <div class="form-group ml-3">
                    <?php echo Form::date("selected_date", request()->get("selected_date", ""), ["class" => "form-control"]); ?>
                </div>

                <button type="submit" class="btn ml-2"><i class="fa fa-filter"></i></button>
                <a href="/scrap/scrap-links" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            </form>
        </div>

        <div class="col-md-12">
            <div class="table-responsive mt-3 col-lg-12 margin-tb">
                <table class="table table-bordered table-striped sort-priority-scrapper">
                    <thead>
                        <tr>
                            <th width="5%">Id</th>
                            <th width="10%">Website</th>
                            <th>Link</th>
                            <th width="10%">Status</th>
                            <th width="10%">Created at</th>
                        </tr>
                    </thead>
                    <tbody class="conent">
                        @foreach ($scrap_links as $links)
                            <tr>
                                <td>{{ $links->id }}</td>
                                <td>{{ $links->website }}</td>
                                <td>{{ $links->links }}</td>
                                <td>{{ $links->status }}</td>
                                <td>{{ $links->created_at }}</td>
                            </tr>
                        @endforeach
                   </tbody>

                </table>
                {{$scrap_links->links()}}
            </div>
        </div>
    </div>
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection
@section('scripts')
@endsection