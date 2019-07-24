@extends('layouts.app')

@section('content')

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body text-center">
                <h4> SEO Analytics </h4>
                <div class="row">
                    <div class="col-md-3">
                        <strong>Domain Authority</strong> <br/> {{ $today->domain_authority }}
                    </div>
                    <div class="col-md-3">
                        <strong>Linking Authority</strong> <br/> {{ $today->linking_authority }}
                    </div>
                    <div class="col-md-3">
                        <strong>Inbound Links</strong> <br/> {{ $today->inbound_links }}
                    </div>
                    <div class="col-md-3">
                        <strong>Ranking Keywords</strong> <br/> {{ $today->ranking_keywords }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-default">

            <div class="panel-body p-0">
                <table class="table border-top">
                    <thead>
                        <th>Date</th>
                        <th>Domain Authority</th>
                        <th>Linking Authority</th>
                        <th>Inbound Links</th>
                        <th>Ranking Keywords</th>
                        <th>Actions</th>
                    </thead>
                    <tbody>
                        @foreach($data as $item)
                            <tr>
                                <td> {{ \Carbon\Carbon::parse($item->created_at)->format('M d, Y') }} </td>
                                <td> {{ $item->domain_authority }} </td>
                                <td> {{ $item->linking_authority }} </td>
                                <td> {{ $item->inbound_links }} </td>
                                <td> {{ $item->ranking_keywords }} </td>
                                <td>
                                    <button class="btn btn-sm btn-danger btn-outline-danger" onclick="deleteEntry('{{ $item->id }}')">
                                        <span class="fa fa-trash"></span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach()
                    </tbody>
                </table>
                <div class="col-md-12 text-center">
                    {!! $data->render() !!}
                </div>
            </div>
        </div>
    </div>
    <script>
        function deleteEntry(id){
            var url  = '{{ route('seo.delete_entry',':id') }}';
            $.ajax({
                url : url.replace(':id', id),
                method : 'POST',
                data : {
                    _token : '{{ csrf_token() }}'
                },
                success : function(response){
                    alert('The entry has been deleted!');
                },
                error : function(error){
                    alert('There was a problem deleting the entry!');
                },
                timeout : function(error){
                    alert('Couldn\'t delete the entr. Please check your internet connectivity!');
                }
            });
        }
    </script>
@endsection()