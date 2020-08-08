@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
@endsection

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Quick Replies</h2>
        </div>

        <div class="col-md-12">
            <div class="infinite-scroll">
                <div class="table-responsive mt-3">
                    <div class="col-md-4 d-inline form-inline">
                        <input style="width: 87%" type="text" name="category_name" placeholder="Enter New Category" class="form-control mb-3 quick_category">
                        <button class="btn btn-secondary quick_category_add">+</button>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            @if(isset($store_websites))
                                    <tr>
                                        <th>#</th>
                                        @foreach($store_websites as $websites)
                                            <th>{{ $websites->title }}</th>
                                        @endforeach
                                    </tr>
                            @endif
                        </thead>
                        <tbody class="tbody">
                            @if(isset($all_categories))
                                    @foreach($all_categories as $all_category)
                                        <tr>
                                            <td>{{ $all_category->name }}</td>
                                            @if(isset($store_websites))
                                                @foreach($store_websites as $websites)
                                                    <td>
                                                        @foreach($category_wise_reply as $key => $value)
                                                            @if($key == $all_category->id)
                                                                @foreach($value as $key1 => $item)
                                                                    @if($key1 == $websites->id)
                                                                        <ul>
                                                                            @foreach($item as $val)
                                                                                <li>{{ $val->reply }}</li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                @endforeach
                                            @endif
                                        </tr>
                                    @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).on('click', '.quick_category_add', function () {
            var textBox = $(this).closest("div").find(".quick_category");
            if (textBox.val() == "") {
                alert("Please Enter Category!!");
                return false;
            }

            var category_count = '{{ $website_length }}';

            $.ajax({
                type: "POST",
                url: "{{ route('add.reply.category') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'name': textBox.val()
                }
            }).done(function (response) {
                textBox.val('');
                var str = '<tr><td>'+ response.data.name +'</td>';
                    for(var i = 0; i < category_count; i++){
                        str += '<td></td>';
                    }
                    str += '</tr>';
                $('.tbody').append(str);
            })
        });
    </script>
@endsection
