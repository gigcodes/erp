@extends('layouts.app')

@section('title', 'WeTransfer Queues')

@section("styles")
    
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">WeTransfer Queues</h2>
             <div class="pull-right">
                <!-- <button type="button" class="btn btn-image" onclick="refreshPage()"><img src="/images/resend2.png" /></button> -->
            </div>

        </div>
    </div>

    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th width="10%">Type</th>
                <th width="10%">URL</th>
                <th width="10%">Supplier</th>
                <th width="10%">Is Processed</th>
                <th width="10%">Updated At</th>
               
            </tr>
            @foreach($wetransfers as $wetransfer)
             <tr>
                    <td>{{ $wetransfer->type }}</td>
                    <td class="expand-row table-hover-cell"><span class="td-mini-container">
                        {{ strlen( $wetransfer->url ) > 50 ? substr( $wetransfer->url , 0, 50).'...' :  $wetransfer->url }}
                        </span>
                        <span class="td-full-container hidden">
                        {{ $wetransfer->url }}
                        </span>
                    </td>
                    <td>{{ $wetransfer->supplier }}</td>
                    <td>@if($wetransfer->is_processed == 1) Pending @elseif($wetransfer->is_processed == 2) Success @else Failed @endif</td>
                    <td>{{ $wetransfer->updated_at->format('d-m-Y : H:i:s') }}</td>     
            </tr>
            @endforeach
            {{ $wetransfers->render() }}
        </thead>
    </table>
</div>

@endsection  

@section('scripts')

<script type="text/javascript">
    //Expand Row
         $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });
</script>

@endsection  