@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('css/bootstrap-datetimepicker.min.css')}}">
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Virtualmin Domains Manage Cloud - {{$domain->name}}</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <!-- <div class="col-8">
                    <form action="{{ route('virtualmin.domains') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-4 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>                            
                            <div class="col-md-3 pd-sm">                                
                                <select name="status" id="status" class="form-control select2">
                                    <option value="">-- Select a status --</option> 
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Enabled</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>
                            <div class="col-md-4 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('virtualmin.domains') }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                            </div>
                        </div>
                    </form>
                </div> -->
                <div class="col-4">
                    <div class="pull-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#a-dns-create">Create A DNS Record</button>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mx-dns-create">Create MX DNS Record</button>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#txt-dns-create">Create TXT DNS Record</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="a-dns-create" class="modal fade in" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create A DNS Record</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <form  method="POST" id="a-dns-create-form">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('name', 'DNS Name', ['class' => 'form-control-label']) !!}
                        {!! Form::text('name', null, ['class'=>'form-control','required']) !!}
                        {!! Form::hidden('Virtual_min_domain_id', $domain->id, ['class'=>'form-control','required']) !!}                        
                    </div>
                    <div class="form-group">
                        {!! Form::label('type', 'Select DNS Type', ['class' => 'form-control-label']) !!}
                        <select name="type" id="type" class="form-control select2">
                            <option value="A">A</option>
                            <option value="cname">CNAME</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary a-dns-save-btn">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="mx-dns-create" class="modal fade in" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create MX DNS Record</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <form  method="POST" id="mx-dns-create-form">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('name', 'DNS Name', ['class' => 'form-control-label']) !!}
                        {!! Form::text('name', null, ['class'=>'form-control','required']) !!}
                        {!! Form::hidden('Virtual_min_domain_id', $domain->id, ['class'=>'form-control','required']) !!}                        
                    </div>
                    <div class="form-group">
                        {!! Form::label('priority', 'Select Priority', ['class' => 'form-control-label']) !!}
                        <select name="priority" id="priority" class="form-control select2">
                            @for ($i = 0; $i <= 100; $i++)
                                <option value="">{{ $i }}</option>
                            @endfor                            
                        </select>
                    </div>
                    <div class="form-group">
                        {!! Form::label('type', 'Select DNS Type', ['class' => 'form-control-label']) !!}
                        <select name="type" id="type" class="form-control select2">
                            <option value="MX">MX</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary mx-dns-save-btn">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="txt-dns-create" class="modal fade in" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create TXT DNS Record</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <form  method="POST" id="txt-dns-create-form">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('name', 'DNS Name', ['class' => 'form-control-label']) !!}
                        {!! Form::text('name', null, ['class'=>'form-control','required']) !!}
                        {!! Form::hidden('Virtual_min_domain_id', $domain->id, ['class'=>'form-control','required']) !!}                        
                    </div>
                    <div class="form-group">
                        {!! Form::label('type', 'Select DNS Type', ['class' => 'form-control-label']) !!}
                        <select name="type" id="type" class="form-control select2">
                            <option value="MX">MX</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary txt-dns-save-btn">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="virtualmin-domains-list">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">Name</th>
                            <th width="20%">Status</th>
                            <th width="20%">Start Date</th>
                            <th width="20%">Expiry Date</th>
                            <th width="5%">Action</th>
                        </tr>
                        @foreach ($domainsDnsRecords as $key => $domain)
                            <tr data-id="{{ $domain->id }}">
                                <td>{{ $domain->id }}</td>
                                
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $domainsDnsRecords->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>

<div id="loading-image-preview" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')50% 50% no-repeat;display:none;">
</div>

<div id="domain-history-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="modal-title"><b>Virtualmin Domains History</b></h4>
                </div>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12" id="domain-history-modal-html">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
    $(document).on("click", ".a-dns-save-btn", function(e) {
        e.preventDefault();
        var $this = $(this);
        $.ajax({
            url: "{{route('virtualmin.domains.createadns')}}",
            type: "post",
            data: $('#a-dns-create-form').serialize()
        }).done(function(response) {
            if (response.code == '200') {
                $('#loading-image').hide();
                toastr['success']('Domain  Created successfully!!!', 'success');
                //location.reload();
            } else if (response.code == '500') {
                $('#loading-image').hide();
                toastr['error'](response.message, 'error');
            } else {
                toastr['error'](response.message, 'error');
            }
        }).fail(function(errObj) {
            $('#loading-image').hide();
            toastr['error'](errObj.message, 'error');
        });
    });
                                
    function listdomainhistory(pageNumber = 1) {
        var button = document.querySelector('.btn.btn-xs.domain-history'); // Corrected class name
        var id = button.getAttribute('data-id');

            $.ajax({
                url: '{{ route('virtualmin.domains.history') }}',
                dataType: "json",
                data: {
                    id: id,
                    page:pageNumber,
                },
                beforeSend: function() {
                $("#loading-image-preview").show();
            }
            }).done(function(response) {
                $('#domain-history-modal-html').empty().html(response.html);
                $('#domain-history-modal').modal('show');
                renderdomainPagination(response.data);
                $("#loading-image-preview").hide();
            }).fail(function(response) {
                $('.loading-image-preview').show();
                console.log(response);
            });
    }

    function renderdomainPagination(response) {
        var paginationContainer = $(".pagination-container-domain");
        var currentPage = response.current_page;
        var totalPages = response.last_page;
        var html = "";
        var maxVisiblePages = 10;

        if (totalPages > 1) {
            html += "<ul class='pagination'>";
            if (currentPage > 1) {
            html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changedomainPage(" + (currentPage - 1) + ")'>Previous</a></li>";
            }
            var startPage = 1;
            var endPage = totalPages;

            if (totalPages > maxVisiblePages) {
            if (currentPage <= Math.ceil(maxVisiblePages / 2)) {
                endPage = maxVisiblePages;
            } else if (currentPage >= totalPages - Math.floor(maxVisiblePages / 2)) {
                startPage = totalPages - maxVisiblePages + 1;
            } else {
                startPage = currentPage - Math.floor(maxVisiblePages / 2);
                endPage = currentPage + Math.ceil(maxVisiblePages / 2) - 1;
            }

            if (startPage > 1) {
                html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changedomainPage(1)'>1</a></li>";
                if (startPage > 2) {
                html += "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                }
            }
            }

            for (var i = startPage; i <= endPage; i++) {
            html += "<li class='page-item " + (currentPage == i ? "active" : "") + "'><a class='page-link' href='javascript:void(0);' onclick='changedomainPage(" + i + ")'>" + i + "</a></li>";
            }
            html += "</ul>";
        }
        paginationContainer.html(html);
     }

    function changedomainPage(pageNumber) {
        listdomainhistory(pageNumber);
    }
</script>
@endsection