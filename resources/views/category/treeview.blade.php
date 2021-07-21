@extends('layouts.app')

    @section('content')
        <style>
            .btn-secondary {
                color: #757575;
                border: 1px solid #ddd;
                background-color: #fff;
            }
            table{
                word-break: break-all;
        }

        </style>

        <h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">
            Category
            <div class="margin-tb" style="flex-grow: 1;">
                <div class="pull-right ">


                    <div class="d-flex justify-content-between  mx-3">

                        <a href="{{ route('category.map-category') }}" class="btn btn-secondary my-0 mr-3">Edit
                            References</a>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#category-popup">
                            Add category
                        </button>
                    </div>
                </div>
            </div>
        </h2>
        <form action="{{ route('category', ['filter' => true]) }}" class="d-block filter_category_form mx-4"
            id="filter_category_form">
            <div class="form-group mb-3">
                <input style="border: 1px solid #ddd;height:30px;border-radius: 4px; padding: 0 5px;" type="text"
                    placeholder="Enter name" name="filter" id="filter_all_category" value="{{ $selected_value }}">
                <button class="btn"><img src="/images/filter.png" style="width:16px"></button>
                <a href="#" onclick="location.reload();
                " type="button" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            </div>
        </form>




        {{-- <!-- Add category modal --> --}}
        <div class="modal fade" id="category-popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Add New Category</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">


                        <form method="POST" action="{{ route('add.category') }}">
                            @csrf
                            <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }} mb-0">
                                <label for="title_category">New Title</label>
                                <input type="text" class="form-control" id="title_category" name="title"
                                    placeholder="Enter title" required>


                            </div>

                            <div class="form-group {{ $errors->has('magento_id') ? 'has-error' : '' }}">

                                <label for="category_magento_id">Magento Id:</label>
                                <input type="text" class="form-control" id="category_magento_id" name="magento_id"
                                    placeholder="Enter Magento Id" required>
                            </div>


                            <div class="form-group {{ $errors->has('show_all_id') ? 'has-error' : '' }}">

                                <label for="category_show_all_id">Show All Id:</label>
                                <input type="text" class="form-control" id="category_show_all_id" name="show_all_id"
                                    placeholder="Enter Show All Id" required>

                            </div>

                            <div class="form-group">
                                <label for="cat_category_segment_id">Select Category Segment:</label>

                               <select class="form-control" name="category_segment_id" id="category_segment_id">
                                    <option>Select Category Segment</option>
                                    @foreach ($category_segments as $k => $catSeg)

                                        <option value="{{ $k }}">{{ $catSeg }}</option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                {!! Form::label('Category:') !!}
                             
                                <?php echo $allCategoriesDropdown; ?>
                                <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                            </div>
                            <div
                                class="d-flex justify-content-between form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                <div>
                                    <input type="checkbox" id="need_to_check_measurement" name="need_to_check_measurement"
                                        value="1">
                                    <label for="need_to_check_measurement"> Need to Check Measurement</label>
                                </div>
                                <div>
                                    <input type="checkbox" id="need_to_check_size" name="need_to_check_size" value="1">
                                    <label for="need_to_check_size"> Need to Check Size</label>
                                </div>
                            </div>


                            <div class="form-group d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button class="btn btn-secondary">Create</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    
        <div class="col-md-12 margin-tb">
            @if ($message = Session::get('error-remove'))
                <div class="alert alert-danger alert-block py-1">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif
            @if ($message = Session::get('success-remove'))
                <div class="alert alert-success alert-block py-1">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif


        <div class="infinite-scroll">
                {!! $categories->appends(request()->query())->links() !!}
            <div class="table-responsive">
                <table class="table table-bordered" style="table-layout:fixed;">
                    <thead>
                        <tr>

                            <th style="width:6%">Level-1 </th>
                            <th style="width:6%">Level-2</th>
                            <th style="width:6%">Level-3</th>
                            <th style="width:6%">Level-4</th>

                            <th style="width:8%"> Magento id</th>
                            <th style="width:8%"> Show all id </th>
                            <th style="width:9%"> SH code </th>
                            <th style="width:7%"> Select category segmanet </th>
                            <th style="width:9%"> Parent category </th>

                            <th style="width:4%"> Check Measurement </th>
                            <th style="width:4%"> Check Size </th>

                            <th style="width:7%"> Push Type</th>
                            
                            <th style="width:3%">Action</th>
                        </tr>
                    </thead>

                    @foreach ($categories as $key => $cat)
                        <tr class="parent-cat">
                            @if ($cat->parentC)
                                @if ($cat->parentC->parentC)
                                    @if ($cat->parentC->parentC->parentC)
                                        <td class="pb-0"> {{ $cat->parentC->parentC->parentC->title  }} </td>
                                        <td class="pb-0"> {{ $cat->parentC->parentC->title  }} </td>
                                        <td class="pb-0">  {{ $cat->parentC->title  }} </td>
                                        <td class="pb-0"> 
                                            <form method="POST"
                                                action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                                class="edit_category_data" data-id={{ $cat->id }}>
                                                @csrf
                                                <div class="d-flex align-items-baseline">
                                                    <div class="form-group mb-0">
    
                                                            <input type="text" class="form-control" name="title"
                                                                   placeholder="Enter Show All Id" value="{{ $cat->title }}">
                                                      </div>
                                                    <button class="btn btn-xs" hidden>
                                                        <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                                    </button>
                                                </div>
                                            </form>     
                                        </td>

                                    @else
                                    <td class="pb-0"> {{ $cat->parentC->parentC->title  }} </td>
                                    <td class="pb-0">  {{ $cat->parentC->title  }} </td>
                                        <td class="pb-0"> 
                                            <form method="POST"
                                                action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                                class="edit_category_data" data-id={{ $cat->id }}>
                                                @csrf
                                                <div class="d-flex align-items-baseline">
                                                    <div class="form-group mb-0">
        
                                                        <input type="text" class="form-control" name="title"
                                                            placeholder="Enter Show All Id" value="{{ $cat->title }}">
                                                    </div>
                                                        <button class="btn btn-xs" hidden>
                                                            <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                                        </button>
                                                </div>
                                            </form>     
                                        
                                        </td>
                                        <td class="pb-0"> - </td>
                                    @endif

                                @else
                                <td class="pb-0">  {{ $cat->parentC->title  }} </td>

                                    <td class="pb-0"> 
                                        <form method="POST"
                                            action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                            class="edit_category_data" data-id={{ $cat->id }}>
                                            @csrf
                                            <div class="d-flex align-items-baseline">
                                                <div class="form-group mb-0">

                                                    <input type="text" class="form-control" name="title"
                                                        placeholder="Enter Show All Id" value="{{ $cat->title }}">
                                                </div>
                                                    <button class="btn btn-xs" hidden>
                                                        <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                                    </button>
                                            </div>
                                        </form> 
                                    </td>
                                    <td class="pb-0"> - </td>
                                    <td class="pb-0"> - </td>
                                @endif

                            @else
                                <td class="pb-0">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                        class="edit_category_data" data-id={{ $cat->id }}>
                                        @csrf
                                        <div class="d-flex align-items-baseline">
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control" name="title"
                                                    placeholder="Enter Show All Id" value="{{ $cat->title }}">
                                            </div>
                                                <button class="btn btn-xs" hidden>
                                                    <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                                </button>
                                        </div>
                                    </form> 
                                </td>
                                <td class="pb-0"> - </td>
                                <td class="pb-0"> - </td>
                                <td class="pb-0"> - </td>
                            @endif

                            <td class="pb-0">
                                <form method="POST"
                                    action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                    class="edit_category_data" data-id="{{ $cat->id }}">
                                    @csrf
                                    <div class="d-flex align-items-baseline">
                                        <div class="form-group mb-0">
                                            <input type="number" class="form-control" name="magento_id"
                                                placeholder="Enter Magento Id" required value="{{ $cat->magento_id }}">
                                        </div>
                                        <button class="btn btn-xs" hidden>
                                            <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                        </button>
                                    </div>
                                </form>
                            </td>
                            <td class="pb-0">
                                <div class="form-group mb-2">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                        class="edit_category_data" data-id={{ $cat->id }}>
                                        @csrf
                                        <div class="d-flex align-items-baseline">
                                            <div class="form-group mb-0 pb-0">

                                                <input type="text" class="form-control" name="show_all_id"
                                                    placeholder="Enter Show All Id" value="{{ $cat->show_all_id }}">
                                            </div>
                                                <button class="btn btn-xs" hidden>
                                                    <img src="/images/filled-sent.png" style="cursor: pointer; width:16px;">
                                                </button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                            <td class="pb-0">
                                <form method="post" action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}" class="edit_category_data"  data-id={{ $cat->id }}>
                                    {{csrf_field()}}
                                
                                    <input type="text" class="form-control" placeholder="Entery HS code" name="simplyduty_code" value="{{ $cat->simplyduty_code }}" >
                                    <button type="button" class="btn btn-secondary btn-block mt-2"  hidden>Save</button>
                                </form>
                            </td>


                            
                            <td class="pb-0">
                                <div class="form-group mb-0 pb-0">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                        class="edit_category_data" data-id={{ $cat->id }}>
                                        @csrf

                                        <select class="form-control submit_on_change globalSelect2"
                                            name="category_segment_id">
                                            <option>Select Category Segment</option>
                                            @foreach ($category_segments as $k => $catSeg)
                                                <option value="{{ $k }}"
                                                    {{ $k == $cat->category_segment_id ? 'selected' : '' }}>
                                                    {{ $catSeg }}</option>
                                            @endforeach
                                        </select>

                                    </form>

                                </div>
                            </td>
                            <td class="pb-0">
                                <form method="POST"
                                    action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                    class="edit_category_data" data-id={{ $cat->id }}>
                                    @csrf
                                    <div class="mb-0 pb-0  form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">

                                        <select name="parent_id" class="submit_on_change globalSelect2">
                                            <option>Select Category </option>

                                            @foreach ($allCategories as $c)
                                                @if ($c->id == $cat->id)
                                                    @continue
                                                @endif

                                                <option value={{ $c->id }}
                                                    {{ $c->id == $cat->parent_id ? 'selected' : '' }}>{{ $c->title }}
                                                </option>
                                            @endforeach

                                        </select>
                                        <span class="text-danger">{{ $errors->first('parent_id') }}</span>

                                    </div>
                                </form>
                            </td>


                            <td class="pb-0">
                                <div
                                    class="d-flex justify-content-between form-group mb-0 {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                        class="edit_category_data" data-id={{ $cat->id }}>
                                        @csrf
                                        <input type="text" name="measurment" value="true" hidden>
                                        <div class="d-flex">

                                            <input type="checkbox" id="edit_need_to_check_measuremen{{ $cat->id }}"
                                                name="need_to_check_measurement" class="submit_on_change"
                                                {{ $cat->need_to_check_measurement ? 'checked' : '' }}>
                                            {{-- <label for="edit_need_to_check_measurement{{ $cat->id }}" class="ml-3"> Check
                                                Measurement</label> --}}
                                        </div>
                                        
                                    </form>

                                </div>

                            </td>
                            <td class="pb-0">
                                <div
                                    class="d-flex justify-content-between form-group mb-0 {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                    <form method="POST"
                                        action="{{ route('category.child-update-category', ['edit' => $cat->id]) }}"
                                        class="edit_category_data" data-id={{ $cat->id }}>
                                        @csrf
                                        <input type="text" name="checkSize" value="true" hidden>

                                        <div class="d-flex">
                                            <input type="checkbox" id="edit_need_to_check_size{{ $cat->id }}"
                                                name="need_to_check_size" {{ $cat->need_to_check_size ? 'checked' : '' }}
                                                class="submit_on_change">
                                            {{-- <label for="edit_need_to_check_size{{ $cat->id }}" class="ml-3"> Check
                                                Size</label> --}}
                                        </div>
                                    </form>

                                </div>

                            </td>
                            

                            {{-- <td class="brand_name" data-id="{{ $cat->title }}">{{ $cat->title }}
                            ({{ count($cat->childs) }})</td> --}}
                            {{-- <td class="created_at">{{ $cat->created_at }}</td> --}}

                            <td class="pb-0">
                                <?php echo Form::select('push_type',[null => "- Select -"] + \App\Category::PUSH_TYPE,$cat->push_type, ["class" => "form-control push-type-change","data-id" => $cat->id]); ?>
                            </td>

                            <td class="pb-0">

                                <form style="display: inline-block" action="{{ route('category.remove') }}" method="POST"
                                    class="category_deleted">
                                    @csrf
                                    <input type="text" name="edit_cat" value={{ $cat->id }} hidden>
                                    <button type="submit" class="btn btn-xs" data-id="{{ $cat->id }}">
                                        <img src="/images/delete.png" style="cursor: pointer; width: 16px;">
                                    </button>
                                </form>

                            </td>
                        </tr>

                        <tr class="add-childs">
                        </tr>
                        {{-- <tr class="expand-{{$cat->brands_id}} hidden">
                    {{-- <tr class="expand-{{$cat->brands_id}} hidden">
                    
                    <td colspan="4" id="attach-image-list-{{$cat->brands_id}}" >
                        
                    </td>
                </tr> --}}
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
        <script type="text/javascript">

                $('ul.pagination').hide();
                $(function() {
                    $('.infinite-scroll').jscroll({
                        autoTrigger: true,
                        loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
                        padding: 2500,
                        nextSelector: '.pagination li.active + li a',
                        contentSelector: 'div.infinite-scroll',
                        callback: function() {
                            
                            $('ul.pagination').remove();
                            $(".select-multiple").select2();
                            initialize_select2();
                        }
                    });
                });

            $(document).on('click', '.show-sub-category', function(e) {
                var subCat = $(this).data('name');
                $this = $(this)

                $this.hasClass('show_child') ? $this.removeClass('show_child') : $this.addClass('show_child')

                if ($this.hasClass('show_child')) {

                    $.ajax({
                        url: "{{ route('category.child-category') }}",
                        method: 'GET',
                        dataType: "json",
                        data: {
                            _token: "{{ csrf_token() }}",
                            'subCat': subCat,
                        },
                        success: function(response) {
                            console.log(response, 'aaaaaaaaaaaaaa')

                            if (response.length) {

                                let html =
                                    '<td colspan="4" class="px-3" style="color: #757575;"><h5 style="color: #000;" class="pl-2 mt-0">Child category</h5><table style="width:100%; ">';

                                response.forEach((element, key) => {
                                    html += `
        
                                        <tr class="parent-cat" colspan="4">
                                    
                                            <td class="index" style="width: 10%"> ${key+1} </td>
                                            <td class="brand_name  style="width: 50%" data-id="${element.id}">${element.title} (${element.child_level_sencond.length})</td>
                                            <td class="created_at" style="width: 20%">${element.created_at}</td>
                                        
                                            <td  style="width: 20%">
                                                <button type="button" class="btn btn-xs no-pd show-sub-category"
                                                            data-id="${ element.id }" data-name="${ element.id }">
                                                            <img src="/images/forward.png" style="cursor: pointer" width="16px">
                                                </button>
                                                <button type="button" class="btn btn-xs category_edit_popup" data-id="${element.id}"
                                                >
                                                    <img src="/images/edit.png" style="cursor: pointer; width: 16px;">
                                                </button>
                                                <form style="display: inline-block" action="{{ route('category.remove') }}" method="POST" class="category_deleted">
                                                    @csrf
                                                    <input type="text" name="edit_cat" value="${element.id}" hidden>
                                                    <button type="submit" class="btn btn-xs" data-id="${element.id}">
                                                    <img src="/images/delete.png" style="cursor: pointer; width: 16px;">
                                                        </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <tr class="add-childs"> 
                                                        
                                                </tr>   
`
                                });
                                html += '</table></td>'
                                $this.closest('.parent-cat').next('.add-childs:first').html(html)
                            } else {
                                let html =
                                    ' <td colspan="4"> <h5 class="text-center mt-0"> No child category available for this<h5> </td>'
                                $this.closest('.parent-cat').next('.add-childs:first').html(html)

                            }

                        },
                        error: function(response) {}
                    });
                } else {
                    $this.closest('.parent-cat').next('.add-childs:first').empty()
                }

            });


            let edited_data_id = null
            $(document).on('click', '.category_edit_popuppp', function(e) {
                e.preventDefault()
                const dataId = $(this).data('id')

                $.ajax({
                    url: "{{ route('category.child-edit-category') }}",
                    method: 'GET',
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        'dataId': dataId,
                    },
                    success: function(response) {
                        console.log(response, 'respose')
                        edited_data_id = response.id
                        $('#edit_title_category').val(response.title)
                        $('#edit_category_magento_id').val(response.magento_id)
                        $('#edit_category_show_all_id').val(response.show_all_id);
                        $('#edit_category_segment_id').val(response.category_segment_id?.id ?? null);
                        $('#edit_need_to_check_measurement').attr('checked', response
                            .need_to_check_measurement ? true : false);
                        $('#edit_need_to_check_size').attr('checked', response.need_to_check_size ? true :
                            false);
                        $('#editCategoryModal').modal('show')
                    },
                    error: function(response) {}
                });
            })

            $(document).on('submit', '.edit_category_data', function(e) {
                e.preventDefault()
                const dataId = $(this).data('id')
                console.log(dataId)
                const form_data = $(this).serialize();
                console.log(form_data, 'form-data')

                $.ajax({
                    url: '/category/' + dataId + '/edit-category',
                    method: 'POST',
                    dataType: "json",
                    data: form_data,
                    success: function(response) {
                        // location.reload()
                        toastr["success"](response['success-remove']);
                        $('#editCategoryModal').modal('hide')

                    },
                    error: function(response) {
                        toastr["error"]("Oops,something went wrong");

                    }
                });

            })

            $(document).on('submit', '.category_deleted', function(e) {
                e.preventDefault()
                $this = $(this);
                const form_data = $(this).serialize();
                // console.log(form_data,'dataid')

                if(confirm('Do you really want to delete this category?')){
                    $.ajax({
                        url: 'category/remove',
                        method: 'POST',
                        dataType: "json",
                        data: $(this).serialize(),
                        success: function(response) {
                            console.log(response)
                            $this.closest('.parent-cat').remove()

                            // location.reload()
                            if (response['error-remove']) {
                                toastr["error"](response['error-remove']);

                            }


                            if (response['success-remove'])
                                toastr["success"](response['success-remove']);
                            // $('#editCategoryModal').modal('hide')
                        },
                        error: function(response) {
                            toastr["error"]("Oops,something went wrong");
                        }
                    });
                }

            })

            // $(document).on('submit','#filter_category_form',function(e){
            //     e.preventDefault()
            //     console.log('its wokting')
            // })

            $(document).on('change', '.submit_on_change', function() {
                $(this).closest('form').submit()
            })

            $(document).on('change','.push-type-change',function() {
                var category_id = $(this).data("id");
                var value = $(this).val();
                $.ajax({
                    url: 'category/change-push-type',
                    method: 'POST',
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        category_id : category_id,
                        value : value
                    },
                    success: function(response) {
                        if(response.code == 200) {
                            toastr["success"](response.message);
                        }else{
                            toastr["error"](response.message);
                        }
                        
                    },
                    error: function(response) {
                        toastr["error"]("Oops,something went wrong");
                    }
                });
            });

        </script>
    @endsection
