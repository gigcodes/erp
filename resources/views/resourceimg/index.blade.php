@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection
@section('content')
    <link href="{{ asset('css/treeview.css') }}" rel="stylesheet">
    <div class="">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <h2 class="page-heading">List Resources Center (<span
                        id="translation_count">{{ $allresources->total() }}</span>)</h2>
                <div class="">
                    <br>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="">Keyword</label>
                                <input name="term" type="text" class="form-control"
                                    value="{{ isset($term) ? $term : '' }}" placeholder="Search keyword"
                                    id="term">
                            </div>
                            <div class="col-md-2">
                                <label for="">Category</label>
                                <select name="category" id="filter_category">
                                    @foreach ($categories as $category)
                                        <option value="{{$category->id}}">{{$category->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="">Sub Category</label>
                                <select name="category" id="filter_sub_category">
                                    @foreach ($sub_categories as $s_category)
                                        <option value="{{$s_category->id}}">{{$s_category->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-image" id='submitSearch'><img
                                        src="/images/filter.png" /></button>
                                <button type="button" class="btn btn-image" id="resetFilter"><img
                                        src="/images/resend2.png" /></button>
                            </div>
                            <div class="col-md-2">
                            </div>
                        </div>
                    </div>
                    <!--  <form action="{{ route('document.index') }}" method="GET">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input name="term" type="text" class="form-control"
                                                           value="{{ isset($term) ? $term : '' }}"
                                                           placeholder="user,department,filename">
                                                </div>
                                                <div class="col-md-3">
                                                    <select class="form-control select-multiple2" name="category[]" data-placeholder="Select Category.." multiple>
                                                        <option>Select Category</option>
                                                        
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class='input-group date' id='filter-date'>
                                                        <input type='text' class="form-control" name="date" value="{{ isset($date) ? $date : '' }}" placeholder="Date" />

                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                      </span>
                                                    </div>
                                                </div>

                                                <div class="col-md-1">
                                                <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                                                </div>

                                            </div>
                                        </div>
                                    </form> -->
                </div>
                <div class="pull-right">
                    <a href="{{ url('/resourceimg/pending/1') }}"><button type="button"
                            class="btn btn-secondary">Pending</button></a>
                    <button type="button" class="btn btn-secondary" title="Add Category" data-toggle="modal"
                        data-target="#addcategory">Add Category</button>
                    <button type="button" class="btn btn-secondary" title="Edit Category" data-toggle="modal"
                        data-target="#editcategory">Edit Category</button>
                    <button type="button" class="btn btn-image" title="Add Resource" data-toggle="modal"
                        data-target="#addresource">
                        <i class="fa fa-plus"></i>
                    </button>

                </div>
            </div>



            <div class="col-lg-12 margin-tb">
                {{-- @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif --}}
                @if ($message = Session::get('danger'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif

                @include('partials.flash_messages')

                <div class="table-responsive col-md-12" style="margin-top : 30px;">
                    <table class="table table-striped table-bordered" id='tblImageResource' style="border: 1px solid #ddd;">
                        <thead>
                            <tr>
                                <th style="width: 2%;">#</th>
                                <th style="width: 10%;">Category</th>
                                <th style="width: 10%;">Sub Category</th>
                                <th style="width: 10%;">Url</th>
                                <th style="width: 10%;">Images</th>
                                <th style="width: 15%;">Created at</th>
                                <th style="width: 10%;">Created by</th>
                            </tr>
                        </thead>
                        <tbody>
                            @include('resourceimg.partial_index')

                        </tbody>
                    </table>
                </div>
                {{ $allresources->render() }}
            </div>
        </div>
    </div>



    @include('resourceimg.partials.modal-create-resource-center')
    @include('resourceimg.partials.modal-create-edit-category')
    <input type="hidden" name='hiddenShowImage' id='hiddenShowImage'data-target="#showresource" data-toggle="modal">
    <div id='modelShowImage'></div>

@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script>
        $(function() {
            $('.selectpicker').selectpicker();
        });

        $('#filter-date').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        $("#filter_sub_category").select2({width: "100%", placeholder: "Select Subcategory", multiple: true})
        $("#filter_category").select2({width: "100%", placeholder: "Select Category", multiple: true})
        $("#filter_sub_category, #filter_category").val(null).trigger('change')
        $(document).ready(function() {
            $('#category_id').select2({ width: "100%" });
            $('#category_id').val(null).trigger('change');
            $('#category_id').change(function (e) { 
                e.preventDefault();
                $('#sub_cat_id').html('');
                // $('#sub_cat_id').multiselect('rebuild');
                // console.log($(this).val());
                // return
                var selected = $(this).val();
                if (selected.length > 0) {
                    $.ajax({
                        url: "{{ url('/api/values-as-per-category') }}",
                        method: "POST",
                        data: {
                            selected: selected,
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(data) {

                            $('#sub_cat_id').html(data);
                            // $('#sub_cat_id').multiselect('rebuild');
                            $("#sub_cat_id").select2("destroy").select2({width: "100%"});
                            $("#sub_cat_id").val(null).trigger('change');
                        }
                    })
                }
            });
            // $('#category_id').multiselect({
            //     nonSelectedText: 'Select Category',
            //     buttonWidth: '300px',
            //     includeSelectAllOption: true,
            //     enableFiltering: true,
            //     enableCaseInsensitiveFiltering: true,

            //     onChange: function(option, checked) {

            //         $('#sub_cat_id').html('');
            //         $('#sub_cat_id').multiselect('rebuild');

            //         var selected = this.$select.val();
            //         if (selected.length > 0) {
            //             $.ajax({
            //                 url: "{{ url('/api/values-as-per-category') }}",
            //                 method: "POST",
            //                 data: {
            //                     selected: selected,
            //                     '_token': '{{ csrf_token() }}'
            //                 },
            //                 success: function(data) {

            //                     $('#sub_cat_id').html(data);
            //                     $('#sub_cat_id').multiselect('rebuild');

            //                 }
            //             })
            //         }
            //     }
            // });
            $('#sub_cat_id').select2({
                width: "100%"    
            });
            // $('#sub_cat_id').multiselect({
            //     nonSelectedText: 'Please Sub Category',
            //     buttonWidth: '300px',
            //     includeSelectAllOption: true,
            //     enableFiltering: true,
            //     enableCaseInsensitiveFiltering: true,
            // });

            $(document).on('click', '#myShowImg', function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ url('show-images/resource') }}",
                    method: "POST",
                    data: {
                        id: $(this).attr("img-id")
                    },
                    success: function(data) {
                        $("#modelShowImage").html(data.html);
                        $("#hiddenShowImage").click();
                    }
                })
            });

            $(document).on('click', '#submitSearch', function() {
                //term = $("#term").val();
                 term =  $("input[name='term']").val();
                 category =  $("#filter_category").val();
                 sub_category =  $("#filter_sub_category").val();
                $.ajax({
                    url: "{{ url('resourceimg') }}",
                    dataType: "json",
                    data: {
                        term: term,
                        sub_category,
                        category
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    },

                }).done(function(data) {
                    $("#loading-image").hide();
                    $("#tblImageResource tbody").empty().html(data.tbody);
                    $("#translation_count").text(data.count);
                    if (data.links.length > 10) {
                        $('ul.pagination').replaceWith(data.links);
                    } else {
                        $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                    }

                }).fail(function(jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
            });
            $(document).on('click', '#resetFilter', function() {
                blank = '';
                $.ajax({
                    url: "{{ url('resourceimg') }}",
                    dataType: "json",
                    data: {
                        blank: blank,
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    },

                }).done(function(data) {
                    $("#loading-image").hide();
                    $('#term').val('')
                    $("#filter_sub_category, #filter_category").val(null).trigger('change')
                    $('#translation-select').val('')
                    $("#tblImageResource tbody").empty().html(data.tbody);
                    $("#translation_count").text(data.count);
                    if (data.links.length > 10) {
                        $('ul.pagination').replaceWith(data.links);
                    } else {
                        $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                    }

                }).fail(function(jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
            });
        });
    </script>
    <script type="text/javascript">
        function PasteImage() {
            var e = document.getElementById("my_canvas").toDataURL();
            $("#cpy_img").val(e), $("#save_img").fadeIn(200), $(".msg").empty(), $(".msg").css("color", "green"), $(".msg")
                .text("Image Loaded Successfully."), $(".can_id").attr("placeholder",
                    "Image Loaded Successfully, Paste another to change."), $("#src_img").attr("src", e)
        }
        var CLIPBOARD = new CLIPBOARD_CLASS("my_canvas", !0);

        function CLIPBOARD_CLASS(e, t) {
            var a = this,
                n = document.getElementById(e),
                i = document.getElementById(e).getContext("2d");
            document.addEventListener("paste", function(e) {
                "can_id" == e.target.id && (console.log(e), a.paste_auto(e))
            }, !1), this.paste_auto = function(e) {
                if (e.clipboardData) {
                    var t = e.clipboardData.items;
                    if (!t) return;
                    for (var a = !1, n = 0; n < t.length; n++)
                        if ($("#cpy_img").val(""), -1 !== t[n].type.indexOf("image")) {
                            var i = t[n].getAsFile(),
                                c = (window.URL || window.webkitURL).createObjectURL(i);
                            this.paste_createImage(c), a = !0
                        } 1 == a ? (e.preventDefault(), $(".msg").text("Image Loading, Please Wait."), $(".msg").css(
                        "color", "red"), setTimeout(PasteImage, 5e3)) : (e.preventDefault(), $(".can_id").attr(
                        "placeholder", "Please paste only image."))
                }
            }, this.paste_createImage = function(e) {
                var a = new Image;
                a.onload = function() {
                    1 == t ? (n.width = a.width, n.height = a.height) : i.clearRect(0, 0, n.width, n.height), i
                        .drawImage(a, 0, 0)
                }, a.src = e
            }
        }
    </script>
    <script src="{{ asset('js/treeview.js') }}"></script>
@endsection
