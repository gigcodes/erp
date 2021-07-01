    @extends('layouts.app')

    @section('content')
        {{-- <button id="show-sub1">click</button> --}}

        <div class="container">
            <div class="table-responsive mt-3">
                <table class="table table-bordered">
                    <tr >
                        <th width="10%">ID</th>
                        <th width="40%">Name</th>
                        <!-- <th width="40%">Logo</th> -->
                        <th width="10%">Created At</th>
                        <th width="10%">Action</th>
                    </tr>

                    @foreach ($categories as $key => $cat)
                        <tr class="parent-cat">
                            <td class="index">{{ $key + 1 }}</td>
                            <td class="brand_name" data-id="{{ $cat->title }}">{{ $cat->title }}
                                {{ count($cat->childs) }}</td>
                            <td class="created_at">{{ $cat->created_at }}</td>

                            <td>
                                <button type="button" class="btn btn-primary no-pd show-sub-category"
                                    data-id="{{ $cat->brands_id }}" data-name="{{ $cat->id }}">
                                    Show subcategory
                                </button>
                            </td>
                        </tr>
                        <tr class="add-childs">

                        </tr>

                        {{-- <tr class="expand-{{$cat->brands_id}} hidden">
                    
                    <td colspan="4" id="attach-image-list-{{$cat->brands_id}}" >
                        
                    </td>
                </tr> --}}
                    @endforeach
                </table>
            </div>


        </div>

        <script type="text/javascript">
            var count = 0;
            
            $(document).on('click', '.show-sub-category', function(e) {
                var subCat = $(this).data('name');
                $this = $(this)
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


                        let html = '<td colspan="4"><table style="width:100%; ">';

                        response.forEach((element, key) => {
                            count = count + key;
                            html += `
        
                <tr class="parent-cat" colspan="4">
              
                    <td class="index"> ${key} </td>
                    <td class="brand_name" data-id="${element.id}">${element.title} ${element.childLevelSencond}</td>
                    <td class="created_at">${element.created_at}</td>
                   
                    <td>
                        <button type="button" class="btn btn-primary no-pd show-sub-category"
                                    data-id="${ element.id }" data-name="${ element.id }">
                                    Show subcategory
                                </button>
                    </td>
                </tr>
                <tr class="add-childs"> 
                            
                        </tr>   
               

`
                        });
                        html += '</table></td>'
                        // console.log(html)    
                        $this.closest('.parent-cat').next('.add-childs:first').html(html)

                    },
                    error: function(response) {
                        toastr['error'](response.responseJSON.message, 'error');
                    }

                });

                console.warn(count);
                // var expand = $('.expand-'+logo_id);
                // $(expand).toggleClass('hidden');
            });
           
        </script>
    @endsection
