@extends('layouts.app')

@section('large_content')
    <style type="text/css">
        .btn-secondary {
            margin-top : 2px;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Categories Map (References)</h2>
        </div>
        <div class="col-md-12">
            <form method="post" id="update-reference-tb" action="{{ action('CategoryController@saveReferences') }}">
                @csrf
                <table class="table table-bordered table-striped sortable-tables">

                    <tr>
                        <th>Category Name</th>
                        <th>Unknown References</th>
                    </tr>
                    <tr>
                        <td colspan="2">Unknown references are shown in 'Unknown Category'. If you want a reference not to appear in here, enter it in 'Ignore Category Reference'. Please note that products with ignored or unknown categories will be linked to those categories.</td>
                    </tr>

                    @foreach($fillerCategories as $category)
                        <tr>
                            <td>
                                {{ $category->title }}
                            </td>
                            <td>
                                <div data-cat-id="{{ $category->id }}" class="col-md-12 category-mov-btn">
                                    @php $options = explode(',', $category->references) @endphp
                                    @if(count($options)>0)
                                        @foreach($options as $option)
                                            @if(strlen($option) > 1)
                                                <span class="btn btn-secondary">{{$option}}</span> 
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                                <!-- <select data-cat-id="{{ $category->id }}" name="category[{{ $category->id }}][]" cols="30" rows="2" class="form-control" multiple>
                                    @php $options = explode(',', $category->references) @endphp
                                    @if(count($options)>0)
                                        @foreach($options as $option)
                                            @if(strlen($option) > 1)
                                                <option selected value="{{$option}}">{{$option}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select> -->
                            </td>
                        </tr>
                    @endforeach

                    <tr>
                        <th>Category Name</th>
                        <th>References</th>
                    </tr>

                    @foreach($categories as $category)
                        <tr>
                            <td>
                                {{ $category->title }}
                            </td>
                            <td>
                                <div data-cat-id="{{ $category->id }}" class="col-md-12 category-mov-btn">
                                    @php $options = explode(',', $category->references) @endphp
                                    @if(count($options)>0)
                                        @foreach($options as $option)
                                            @if(strlen($option) > 1)
                                                <span class="btn btn-secondary">{{$option}}</span> 
                                            @endif
                                        @endforeach
                                    @endif
                                </div>    

                                <!-- <select data-cat-id="{{ $category->id }}" name="category[{{ $category->id }}][]" cols="30" rows="2" class="form-control" multiple>
                                    @php $options = explode(',', $category->references) @endphp
                                    @if(count($options)>0)
                                        @foreach($options as $option)
                                            @if(strlen($option) > 1)
                                                <option selected value="{{$option}}">{{$option}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select> -->
                            </td>
                        </tr>
                        <!-- get sub categories -->
                        @php
                            $subcategories = \App\Category::where( 'id', '>', 1 )->where('parent_id', $category->id)->get();
                        @endphp
                        @if ( $subcategories != null )
                            @foreach($subcategories as $subcategory)
                                <tr>
                                    <td>
                                        {{ $category->title }} &gt; {{ $subcategory->title }}
                                    </td>
                                    <td>
                                        <div data-cat-id="{{ $subcategory->id }}" class="col-md-12 category-mov-btn">
                                            @php $options = explode(',', $subcategory->references) @endphp
                                            @if(count($options)>0)
                                                @foreach($options as $option)
                                                    @if(strlen($option) > 1)
                                                        <span class="btn btn-secondary">{{$option}}</span> 
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                        <!-- <select data-cat-id="{{ $category->id }}" name="category[{{ $subcategory->id }}][]" cols="30" rows="2" class="form-control" multiple>
                                            @php $options = explode(',', $subcategory->references) @endphp
                                            @if(count($options)>0)
                                                @foreach($options as $option)
                                                    @if(strlen($option) > 1)
                                                        <option selected value="{{$option}}">{{$option}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select> -->
                                    </td>
                                </tr>
                                <!-- get sub categories -->
                                @php
                                    $sscategories = \App\Category::where( 'id', '>', 1 )->where('parent_id', $subcategory->id)->get();
                                @endphp
                                @if ( $sscategories != null )
                                    @foreach($sscategories as $sscategory)
                                        <tr>
                                            <td>
                                                {{ $category->title }} &gt; {{ $subcategory->title }} &gt; {{ $sscategory->title }}
                                            </td>
                                            <td>
                                                <div data-cat-id="{{ $sscategory->id }}" class="col-md-12 category-mov-btn">
                                                    @php $options = explode(',', $sscategory->references) @endphp
                                                    @if(count($options)>0)
                                                        @foreach($options as $option)
                                                            @if(strlen($option) > 1)
                                                                <span class="btn btn-secondary">{{$option}}</span> 
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <!-- <select data-cat-id="{{ $category->id }}" name="category[{{ $sscategory->id }}][]" cols="30" rows="2" class="form-control" multiple>
                                                    @php $options = explode(',', $sscategory->references) @endphp
                                                    @if(count($options)>0)
                                                        @foreach($options as $option)
                                                            @if(strlen($option) > 1)
                                                                <option selected value="{{$option}}">{{$option}}</option>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select> -->
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                    <tr>
                        <td colspan="2" class="text-center">
                            <button class="btn btn-default">Save</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>
        /*$("select").select2({
            tags: true
        });*/

        $(".sortable-tables").find(".category-mov-btn").find("span").draggable({
            //containment : ".category-mov-btn",
            appendto : false
        });

        $(".sortable-tables").find(".category-mov-btn").droppable({
          drop: function( event, ui ) {
            //Get dragged Element (checked)
              draggedElement = $(ui.draggable);

              //Get dropZone where element is dropped (checked)
              dropZone = $(event.target);

              //Move element from list, to dropZone (Change Parent, Checked)
              $(dropZone).append(draggedElement);

              //Get current position of draggable (relative to document)
              var offset = $(ui.helper).offset();
              xPos = offset.left;
              yPos = offset.top;

              //Move back element to dropped position
              $(draggedElement).css('top', 2).css('left', 2);

              var catId = [];
              window.catId = [];

              var iterate = $(".category-mov-btn").each(function(k,v){
                 var $this = $(v);
                 var categoryId = $this.data("cat-id");
                 var allTypes = [];
                    $.each($this.find("span"),function(k,v){
                        var $span = $(v);
                        allTypes.push($span.text());
                    });
                    var keyName = "cat_"+categoryId;
                    window.catId.push("cat_"+categoryId + "#" + allTypes.join(","));
              });

              $.when(iterate).then(  function() {
                $.ajax({
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        },
                        url: '/category/references',
                        data: {info : window.catId},
                    }).done(response => {
                        toastr['success']('Category Updated successfully', 'success');
                    });
              } );
          }
        });

        /*$( ".sortable-tables" ).sortable({
            items : $(".sortable-tables").find(".category-mov-btn").find("button"),
            start: function(event, ui) {
              console.log(ui.item);
            },
            update: function(e,ui){
                //var allCategories =                
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/category/references',
                    data: $("#update-reference-tb").serialize()
                }).done(response => {
                    
                });
            }
        });*/

    </script>
@endsection