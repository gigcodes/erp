@extends('layouts.app')

@section('title', 'Google Search')


@section('styles')
<style type="text/css">
    .switch {
  position: relative;
  display: inline-block;
  width: 40px;
  height: 24px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

.filter-icon {
    font-size: 16px;
}

.btn-trash {
    font-size: 16px;
}

input:checked + .slider:before {
  -webkit-transform: translateX(16px);
  -ms-transform: translateX(16px);
  transform: translateX(16px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
 #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
</style>
@endsection
@section('content')
@include('partials.flash_messages')
 <div id="myDiv">
      <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 px-0">
                <h2 class="page-heading">Google Search Keywords (<span>{{ $keywords->total() }}</span>) </h2>
            </div>
            <div class="col-md-12">
                @if(Session::has('message'))
                <script>
                    alert("{{Session::get('message')}}")
                </script>
                @endif
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <form action="{{ route('google.search.keyword') }}" method="GET">
                            <div class="form-group">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <input name="term" type="text" class="form-control"
                                               value="{{ isset($term) ? $term : '' }}"
                                               placeholder="Keyword Name">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="form-check d-flex align-items-center pl-0">
                                                <input class="form-check-input mt-0" type="checkbox" name="priority" id="defaultCheck1">
                                                <label class="form-check-label pl-4 ml-2 font-weight-normal" for="defaultCheck1">
                                                    Priority
                                                </label>
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-secondary ml-4"><i class="fa fa-filter filter-icon" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    <div class="col-md-6">

                        <form method="post" action="{{ action([\App\Http\Controllers\GoogleSearchController::class, 'store']) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Keyword</label>
                                        <input type="text" name="name" id="name" placeholder="sololuxuryindia" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Add?</label>
                                        <button class="btn-block btn btn-primary">Add Keyword</button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="platform_id" value="2">
                        </form>

                    </div>
                </div>



                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addVariantModal">
                    Add Variant
                </button>
                <!-- Button Generate Keyword Strings -->
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#generateStringModal">
                    Generate Strings
                </button>


            </div>
            <div class="col-md-12">
                <table class="table-striped table-bordered table table-sm">
                    <tr>
                        <th>S.N</th>
                        <th><a href="/google/search/keyword{{ ($queryString) ? '?'.$queryString : '?' }}sortby=keyword{{ ($orderBy == 'DESC') ? '&orderby=ASC' : '' }}">Keyword</a></th>
                        <th>Priority</th>
                        <th>Run Scraper</th>
                        <th>Actions</th>
                    </tr>
                    @foreach($keywords as $key=>$keyword)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>
                            {{ $keyword->hashtag }}
                        </td>
                        <td>
                            <label class="switch mb-0">
                                @if($keyword->priority == 1)
                                <input type="checkbox" checked class="checkbox" value="{{ $keyword->id }}">
                                @else
                                <input type="checkbox" class="checkbox" value="{{ $keyword->id }}">
                                @endif
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <button class="btn py-0 btn-default " id="runScrapper_{{ $keyword->id }}" onclick="callScraper({{ $keyword->id }})">Run Scraper For {{ $keyword->hashtag }}</button>
                        </td>
                        <td>
                            <form method="post" action="{{ action([\App\Http\Controllers\GoogleSearchController::class, 'destroy'], $keyword->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-default btn-trash btn-image border-0 btn-sm">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </table>
                {!! $keywords->appends(Request::except('page'))->links() !!}
            </div>
        </div>
    </div>
<!-- Add variants modal -->
<div class="modal fade " id="addVariantModal" tabindex="-1" role="dialog" aria-labelledby="addVariantModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Keyword Variants</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Variant</label>
                                <input type="text" name="variant_name" id="variant_name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Add?</label>
                                <button type="button" class="btn-block btn btn-primary" id="add_keyword_variant">Add Variant</button>
                            </div>
                        </div>
                    </div>

                    <table id="variant_list_table" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th width="16%">ID</th>
                                <th width="16%">Variants</th>
                                <th width="16%">Date</th>
                                <th width="16%">Action</th>
                            </tr>
                        </thead>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Generate Strings modal -->
<div class="modal fade " id="generateStringModal" tabindex="-1" role="dialog" aria-labelledby="generateStringModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Generate String</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post">
<!--                    <div class="row">-->
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Brand:</strong>
                                <?php echo Form::select("brand", [], null, ["class" => "form-control brand-list", 'id' => 'brand-list']); ?>
                                <span class="product-title-show"></span>
                            </div>
                        </div>
<!--                    </div>-->

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Select Category</label>
                            {!! $new_category_selection !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Select Variants</label>
<!--                            {!! $new_category_selection !!}-->

                            <select class="form-control w-100 select-multiple" name="variants[]" data-placeholder=" Select variant" multiple>
                                @foreach($variants as $key => $variant)
                                <option value="{{ $key }}" >{{ $variant }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.select-multiple').select2({width: '100%'});

        $(".checkbox").change(function() {
            id = $(this).val();
               
            if(this.checked) {
               $.ajax({
                    type: 'GET',
                    url: '{{ route('google.search.keyword.priority') }}',
                    data: {
                        id:id,
                        type: 1
                    },success: function (data) {
                      console.log(data);
                        if(data.status == 'error'){
                           alert('Priority Limit Exceded'); 
                           location.reload(true);
                           
                        }else{
                           alert('Keyword Priority Added');  

                        }
                      
                    },
                    error: function (data) {
                       alert('Priority Limit Exceded');
                    }
                        });
            }else{
                 $.ajax({
                    type: 'GET',
                    url: '{{ route('google.search.keyword.priority') }}',
                    data: {
                        id:id,
                        type: 0
                    },
                        }).done(response => {
                         alert('Keyword Removed Priority');    
                    }); 
            }
        });


        $('#variant_list_table').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "searching":true,
            "ajax": "{{ route('list.keyword.variant') }}",
            "columns": [
                { "data": null},
                { "data": "keyword" },
                { "data": "created_at",
                    "render": function (data, type, row) {
                        return moment(data).format('DD-MM-YYYY');
                    }
                },
                { "data": null },
            ],
            "columnDefs": [
                {
                    "targets": 3,
                    "data": null,
                    "render": function (data, type, row, meta) {
                        return '<a href="javascript:void(0)" class="text-danger" onclick="deleteRow('+data.id+')"><i class="fa fa-trash"></i></a>';
                    }
                }
            ],
            "createdRow": function (row, data, index) {
                $('td', row).eq(0).html(index + 1);
            }
        });
    });

    function deleteRow(id) {
        $.ajax({
            url: "{{ url('variant') }}/" + id,
            type: 'DELETE',
            headers: {
                "X-CSRF-TOKEN": "{{csrf_token()}}"
            },
            success: function(data) {
                $('#variant_list_table').DataTable().ajax.reload();
                if (data.error){
                    toastr['error'](data.error);
                } else {
                    $('#runScrapper_'+id).prop('disabled', false);
                    $('#runScrapper_'+id).html(buttonCaption);
                    alert('Scrapper initiated successfully');
                }
            }
        });
    }

    function callScraper(id){
        var buttonCaption = $('#runScrapper_'+id).html();
        $('#runScrapper_'+id).html('Initiating...');
        $('#runScrapper_'+id).prop('disabled', true);
        //ajax call coming here...
        $.ajax({
            url: "{{ route('google.search.keyword.scrap') }}",
            type: 'GET',
            data: {
                id: id,
                _token: "{{ csrf_token() }}"
            },            
            success: function(data) {
                if (data.error){
                    toastr['error'](data.error);
                } else {
                    $('#runScrapper_'+id).prop('disabled', false);
                    $('#runScrapper_'+id).html(buttonCaption);
                    alert('Scrapper initiated successfully');
                }
            }
        });
    }

    $('#add_keyword_variant').on('click', function(e) {

        e.preventDefault();
        var name = $('#variant_name').val();
        $.ajax({
            type: "POST",
            url: "{{ route('add.keyword.variant') }}",
            data: {keyword:name,  _token: "{{ csrf_token() }}"},
            success: function( msg ) {
                $('#variant_list_table').DataTable().ajax.reload();
                $('#variant_name').val('');
            }
        });
    });

    function generateString() {
        $.ajax({
            type: "POST",
            url: "{{ route('keyword.generate') }}",
            data: {keyword:name,  _token: "{{ csrf_token() }}"},
            success: function( msg ) {
                $('#variant_list_table').DataTable().ajax.reload();
                $('#variant_name').val('');
            }
        });
    }

    $('#brand-list').select2({
        width: '100%',
        ajax: {
            url: '{{ route("brand.list") }}',
            processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                return {
                    results: data.items
                };
            }
        }
    });
    </script>
@endsection