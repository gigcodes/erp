@extends('layouts.app')

@section('styles')

<style type="text/css">
    #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
</style>
@endsection
@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Product translations</h2>
            <div class="pull-left">
                <div class="form-group">
                        <div class="row">
                            <div class="col-md-10">
                                <input id="term" name="term" type="text" class="form-control"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="Search" id="term">
                            </div>
                            <div class="col-md-2">
                               <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="/images/filter.png"/></button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered" id="translation-table">
              <thead>
            <tr>
                <th>Id</th>
                <th>Title</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @include('products.translations.product-search')
            </tbody>
        </table>
        {{$product_translations->links()}}
    </div>

    <div id="translationModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" id="translated-content">
            </div>
        </div>
    </div>




@endsection


@section('scripts')
    <script>

            function submitSearch(){
                src = '/products/product-translation'
                term = $('#term').val()
                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {
                        term : term

                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },

                }).done(function (data) {
                    $("#loading-image").hide();
                    $("#translation-table tbody").empty().html(data.tbody);
                    if (data.links.length > 10) {
                        $('ul.pagination').replaceWith(data.links);
                    } else {
                        $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                    }

                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
                
            }
        $(document).ready(function() {
            $(document).on('click', '.view-btn', function (e) {
                var id = $(this).attr("data-id");
                $.ajax({
                    url: '/products/product-translation/'+id,
                    type: 'GET',
                    success: function(response) {
                        $("#translated-content").html(response);
                    }
                })
            });

            $(document).on('click', '.edit-translation', function (e) {
                
                var locale = $('#select-locale').val();
                var product_id = $('#product_id').val();
                var product_translation_id = $('#product_translation_id').val();
                var title = $('#title').val();
                var description = $('#description').val();

                $.ajax({
                    url: '/products/product-translation/submit/'+product_translation_id,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        title : title,
                        description : description,
                        product_id: product_id
                    },
                    success: function(response) {
                        alert(response.message);
                        $('#translationModal').modal('hide');
                    }
                })
            });
        });

        $(document).on('change', 'select', function (e) {
            var locale = $('#select-locale').val();
            var id = $('#product_id').val();
            $.ajax({
                    url: '/products/product-translation/details/'+id+'/'+locale,
                    type: 'GET',
                    success: function(response) {
                        if(response.product_translation) {
                            $('#title').val(response.product_translation.title);
                            $('#description').val(response.product_translation.description);
                            $('#product_translation_id').val(response.product_translation.id);
                        }
                        else {
                            $('#title').val('');
                            $('#description').val('');
                        }
                }
            })
        });
    </script>
@endsection