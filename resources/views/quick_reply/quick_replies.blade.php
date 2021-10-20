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
        <div class="col-md-12 form-inline">
            <input type="text" name="category_name" placeholder="Enter New Category" class="form-control quick_category">
            <button class="btn btn-xs quick_category_add ml-3"><i class="fa fa-plus"></i></button>
            {{Form::model( [], array('method'=>'get', 'class'=>'form-inline')) }}
                <div class="form-group ml-3 cls_filter_inputbox">
                    {{Form::select('sub_category', $sub_categories, $subcat, array('class'=>'form-control'))}}
                </div>
                <button type="submit" class="btn btn-xs ml-3"><i class="fa fa-filter"></i></button>
            </form>
        </div>
        <div class="col-md-12">
            <div class="infinite-scroll">
                <div class="table-responsive mt-3">
                    
                    <table class="table table-bordered">
                        <thead>
                            @if(isset($store_websites))
                                    <tr>
                                        <th width="15%">Category</th>
                                        <th width="10%">S&nbsp;Category</th>
                                        <th width="10%">S S&nbsp;Category</th>
                                        @foreach($store_websites as $websites)
                                        <?php 
                                        $title = $websites->title;
                                        $title= str_replace(' & ','&',$title);
                                        $title= str_replace(' - ','-',$title);
                                        $title= str_replace('&',' & ',$title);
                                        $title= str_replace('-',' - ',$title);
                                        $words = explode(' ', $title);
                                        if (count($words) >= 2) {
                                            $title='';
                                            foreach($words as $word){
                                                $title.=strtoupper(substr($word, 0, 1));
                                            }
                                        }
                                        
                                        ?>
                                            <th width="6%">{{ $title }}</th>
                                        @endforeach
                                    </tr>
                            @endif
                        </thead>

                        <tbody class="tbody">
                            @if(isset($all_categories))
                                    @foreach($all_categories as $all_category)
                                        <tr>
                                            
                                            <td class="p-0 pt-1 pl-1">
                                                <div id="show_add_sub_{{ $all_category->id }}" class="hide_all_inputs_sub" style="display: none;">
                                                    <input type="text" id="reply_sub_{{ $all_category->id }}" class="reply_inputs_sub form-control w-75 pull-left"/>
                                                    <button class="btn btn-sm p-0 pt-2 save_reply_sub pull-left w-25"><i class="fa fa-check"></i></button>
                                                </div>
                                                <div id="show_reply_list_sub_{{ $all_category->id }}" class="w-100 pull-left">
                                                    <span>{{ $all_category->name }}</span>  
                                                    <a href="javascript::void()" class="add_sub_cat btn btn-sm p-0" id="show_add_option_sub_{{ $all_category->id }}" data-id="{{ $all_category->id }}"><i class="fa fa-plus"></i></a> 
                                                </div>
                                            </td>
                                             <td></td>
                                             <td></td>
                                            @if(isset($store_websites))
                                                @foreach($store_websites as $websites)

                                                    <td class="p-0 pt-1 pl-1">
                                                        <div id="show_add_reply_{{ $all_category->id }}_{{ $websites->id }}" class="hide_all_inputs" style="display: none;">
                                                            <input type="text" id="reply_{{ $all_category->id }}_{{ $websites->id }}" class="reply_inputs form-control pull-left" style="width: 80px;"/>
                                                            <button class="btn btn-sm p-0 save_reply pull-left"><i class="fa fa-check"></i></button>
                                                        </div>

                                                        <div id="show_reply_list_{{ $all_category->id }}_{{ $websites->id }}">
                                                            <span class="show_add_option pull-left" id="show_add_option_{{ $all_category->id }}_{{ $websites->id }}">
                                                                <a href="javascript::void(0)" class="add_quick_reply btn btn-sm p-0" id="{{ $all_category->id }}" data-attr="{{ $websites->id }}"><i class="fa fa-plus"></i></a>
                                                            </span>
                                                            @foreach($category_wise_reply as $key => $value)
                                                                @if($key == $all_category->id)
                                                                    @foreach($value as $key1 => $item)
                                                                        @if($key1 == $websites->id)
                                                                        <button class="btn btn-sm p-0 lead_summary pull-left" data-toggle="modal" data-target="#replies{{ $all_category->id}}-{{$websites->id}}"><i class="fa fa-info-circle"></i></button>
                                                                               
                                                                             
                                                                                <div class="modal fade" id="replies{{ $all_category->id}}-{{$websites->id}}" tabindex="-1" role="dialog" aria-labelledby="replies" aria-hidden="true">
                                                                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header p-0 pt-2 pl-2 pr-2">
                                                                                            <h5 class="modal-title" id="exampleModalLongTitle">All Replies</h5>
                                                                                            <button type="button" class="close btn-xs p-0 mr-2" data-dismiss="modal" aria-label="Close">
                                                                                                <i class="fa fa-times"></i>
                                                                                            </button>
                                                                                        </div>
                                                                                        <div class="modal-body edit-modal-body" id="all-replies">
                                                                                        <ul class="list-group">
                                                                                        @foreach($item as $val)
                                                                                        <li id="edit_reply_{{ $val->id }}" class="edit_reply_input list-group-item p-2" style="display: none;">
                                                                                            <input type="text" value="{{ $val->reply }}" id="edit_reply_{{ $val->id }}" class="form-control w-75 pull-left" />
                                                                                            <button class="btn btn-sm p-0 pt-2 update_reply w-25 pull-left"><i class="fa fa-check"></i></button>
                                                                                        </li>
                                                                                        <li id="{{ $val->id }}" class="edit_reply list-group-item p-2" style="overflow-wrap:break-word;">
                                                                                            {{ $val->reply }}
                                                                                        </li>
                                                                                        @endforeach
                                                                                        </ul>
                                                                                        </div>
                                                                                        </form>	
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                               
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </td>

                                                @endforeach
                                            @endif

                                        </tr>
                                        @if($all_category['childs'])

                                                @foreach($all_category['childs'] as $all_category_sub)
                                                <tr>
                                                    <td></td>
                                                    <td class="p-0 pt-1 pl-1">
                                                
                                                    <div id="edit_reply_sub_{{ $all_category_sub->id }}" class="edit_reply_input_sub" style="display: none;">
                                                        <input type="text" value="{{ $all_category_sub->name }}" id="edit_reply_sub_{{ $all_category_sub->id }}" />
                                                        <button class="btn btn-secondary btn-sm update_reply_sub">&#10004;</button>
                                                    </div>  
                                               

                                                    <div id="show_add_sub_sub_{{ $all_category_sub->id }}" class="hide_all_inputs_sub_sub" style="display: none;">
                                                        <input type="text" id="reply_sub_sub_{{ $all_category_sub->id }}" class="reply_inputs_sub_sub form-control w-75 pull-left"/>
                                                        <button class="btn btn-sm p-0 pt-2 save_reply_sub_sub pull-left w-25"><i class="fa fa-check"></i></button>
                                                    </div>

                                                    <div id="show_reply_list_sub_sub_{{ $all_category_sub->id }}" class="w-100 pull-left">
                                                        <span id="{{ $all_category_sub->id }}" class="edit_reply_sub">{{ $all_category_sub->name }}</span>  
                                                        <a href="javascript:void(0)" class="add_sub_cat_sub btn btn-sm p-0" id="show_add_option_sub_sub_{{ $all_category_sub->id }}" data-id="{{ $all_category_sub->id }}"><i class="fa fa-plus"></i></a> 
                                                    </div>
                                               
                                                    </td>
                                                    <td></td>
                                                    @if(isset($store_websites))
                                                    @foreach($store_websites as $websites)

                                                        <td class="p-0 pt-1 pl-1">
                                                            <div id="show_add_reply_{{ $all_category_sub->id }}_{{ $websites->id }}" class="hide_all_inputs" style="display: none;">
                                                                <input type="text" id="reply_{{ $all_category_sub->id }}_{{ $websites->id }}" class="reply_inputs form-control pull-left" style="width: 80px;"/>
                                                                <button class="btn btn-sm p-0 save_reply pull-left"><i class="fa fa-check"></i></button>
                                                            </div>

                                                            <div id="show_reply_list_{{ $all_category_sub->id }}_{{ $websites->id }}">
                                                                <span class="show_add_option pull-left" id="show_add_option_{{ $all_category_sub->id }}_{{ $websites->id }}">
                                                                    <a href="javascript::void(0)"  class="add_quick_reply btn btn-sm p-0" id="{{ $all_category_sub->id }}" data-attr="{{ $websites->id }}"><i class="fa fa-plus"></i></a>
</span>
                                                                @foreach($category_wise_reply as $key => $value)
                                                                    @if($key == $all_category_sub->id)
                                                                        @foreach($value as $key1 => $item)
                                                                            @if($key1 == $websites->id)
                                                                            <button class="btn btn-sm p-0 lead_summary pull-left" data-toggle="modal" data-target="#replies{{ $all_category_sub->id}}-{{$websites->id}}"><i class="fa fa-info-circle"></i></button>
                                                                            <div class="modal fade" id="replies{{ $all_category_sub->id}}-{{$websites->id}}" tabindex="-1" role="dialog" aria-labelledby="replies" aria-hidden="true">
                                                                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header p-0 pt-2 pl-2 pr-2">
                                                                                            <h5 class="modal-title" id="exampleModalLongTitle">All Replies</h5>
                                                                                            <button type="button" class="close btn-xs p-0 mr-2" data-dismiss="modal" aria-label="Close">
                                                                                                <i class="fa fa-times"></i>
                                                                                            </button>
                                                                                        </div>
                                                                                        
                                                                                        <div class="modal-body edit-modal-body" id="all-replies">
                                                                                        <ul class="list-group">
                                                                                        @foreach($item as $val)
                                                                                        <li id="edit_reply_{{ $val->id }}" class="edit_reply_input list-group-item p-2" style="display: none;">
                                                                                            <input type="text" value="{{ $val->reply }}" id="edit_reply_{{ $val->id }}" class="form-control w-75 pull-left" />
                                                                                            <button class="btn btn-sm p-0 pt-2 update_reply w-25 pull-left"><i class="fa fa-check"></i></button>
                                                                                        </li>
                                                                                        <li id="{{ $val->id }}" class="edit_reply list-group-item p-2" style="overflow-wrap:break-word;">{{ $val->reply }}</li>
                                                                                        @endforeach
                                                                                        </ul>
                                                                                        </div>
                                                                                        </form>	
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                @endforeach
</div>
                                                        </td>

                                                    @endforeach
                                                @endif
                                                </tr>
                                                @if($all_category_sub['subchilds'])

                                                @foreach($all_category_sub['subchilds'] as $all_category_sub_sub)
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="p-0 pt-1 pl-1">
                                                
                                                    <div id="edit_reply_sub_{{ $all_category_sub_sub->id }}" class="edit_reply_input_sub" style="display: none;">
                                                        <input type="text" value="{{ $all_category_sub_sub->name }}" id="edit_reply_sub_{{ $all_category_sub_sub->id }}" />
                                                        <button class="btn btn-secondary btn-sm update_reply_sub">&#10004;</button>
                                                    </div>  
                                               

                                                    <span id="{{ $all_category_sub_sub->id }}" class="edit_reply_sub">{{ $all_category_sub_sub->name }}</span>  
                                               
                                                    </td>
                                                    @if(isset($store_websites))
                                                    @foreach($store_websites as $websites)

                                                        <td class="p-0 pt-1 pl-1">
                                                            <div id="show_add_reply_{{ $all_category_sub_sub->id }}_{{ $websites->id }}" class="hide_all_inputs" style="display: none;">
                                                                <input type="text" id="reply_{{ $all_category_sub_sub->id }}_{{ $websites->id }}" class="reply_inputs form-control pull-left" style="width: 80px;"/>
                                                                <button class="btn btn-sm p-0 save_reply pull-left"><i class="fa fa-check"></i></button>
                                                            </div>

                                                            <div id="show_reply_list_{{ $all_category_sub_sub->id }}_{{ $websites->id }}">
                                                                <span class="show_add_option pull-left" id="show_add_option_{{ $all_category_sub_sub->id }}_{{ $websites->id }}">
                                                                    <a href="javascript::void(0)"  class="add_quick_reply btn btn-sm p-0" id="{{ $all_category_sub_sub->id }}" data-attr="{{ $websites->id }}"><i class="fa fa-plus"></i></a>
</span>
                                                                @foreach($category_wise_reply as $key => $value)
                                                                    @if($key == $all_category_sub_sub->id)
                                                                        @foreach($value as $key1 => $item)
                                                                            @if($key1 == $websites->id)
                                                                            <button class="btn btn-sm p-0 lead_summary pull-left" data-toggle="modal" data-target="#replies{{ $all_category_sub_sub->id}}-{{$websites->id}}"><i class="fa fa-info-circle"></i></button>
                                                                            <div class="modal fade" id="replies{{ $all_category_sub_sub->id}}-{{$websites->id}}" tabindex="-1" role="dialog" aria-labelledby="replies" aria-hidden="true">
                                                                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header p-0 pt-2 pl-2 pr-2">
                                                                                            <h5 class="modal-title" id="exampleModalLongTitle">All Replies</h5>
                                                                                            <button type="button" class="close btn-xs p-0 mr-2" data-dismiss="modal" aria-label="Close">
                                                                                                <i class="fa fa-times"></i>
                                                                                            </button>
                                                                                        </div>
                                                                                        
                                                                                        <div class="modal-body edit-modal-body" id="all-replies">
                                                                                        <ul class="list-group">
                                                                                        @foreach($item as $val)
                                                                                        <li id="edit_reply_{{ $val->id }}" class="edit_reply_input list-group-item p-2" style="display: none;">
                                                                                            <input type="text" value="{{ $val->reply }}" id="edit_reply_{{ $val->id }}" class="form-control w-75 pull-left" />
                                                                                            <button class="btn btn-sm p-0 pt-2 update_reply w-25 pull-left"><i class="fa fa-check"></i></button>
                                                                                        </li>
                                                                                        <li id="{{ $val->id }}" class="edit_reply list-group-item p-2" style="overflow-wrap:break-word;">{{ $val->reply }}</li>
                                                                                        @endforeach
                                                                                        </ul>
                                                                                        </div>
                                                                                        </form>	
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                @endforeach
</div>
                                                        </td>

                                                    @endforeach
                                                @endif
                                                </tr>
                                                @endforeach

                                             @endif
                                                @endforeach

                                             @endif


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
        $(document).ready(function(){
            $('.hide_all_inputs').hide();
            $('.hide_all_inputs_sub').hide();
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

            var cat_id, store_id;
            $('.add_quick_reply').on("click", function(){
                $('.hide_all_inputs').hide();
                $('.show_add_option').show();
                $('.reply_inputs').val('');
                cat_id = $(this).attr('id');
                store_id = $(this).data('attr');
                $('#show_add_option_'+cat_id+'_'+store_id).hide();
                $('#show_add_reply_'+cat_id+'_'+store_id).show();
            });

            var cat_sub_id;
            $('.add_sub_cat').on("click", function(){
                $('.hide_all_inputs_sub').hide();
                $('.add_sub_cat').show();
                $('.reply_inputs_sub').val('');
                cat_sub_id = $(this).attr('data-id');
                $('#show_add_option_sub_'+cat_sub_id).hide();
                $('#show_add_sub_'+cat_sub_id).show();
            });

            var cat_sub_sub_id;
            $('.add_sub_cat_sub').on("click", function(){
                $('.hide_all_inputs_sub_sub').hide();
                $('.add_sub_cat_sub').show();
                $('.reply_inputs_sub_sub').val('');
                cat_sub_sub_id = $(this).attr('data-id');
                $('#show_add_option_sub_sub_'+cat_sub_sub_id).hide();
                $('#show_add_sub_sub_'+cat_sub_sub_id).show();
            });

            $(document).on('click','.save_reply',function(){
                var reply = $('#reply_'+cat_id+'_'+store_id).val();
                if(reply == ''){
                    alert('Please enter reply');
                    return false;
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('save-store-wise-reply') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'reply': reply,
                        'category_id': cat_id,
                        'store_website_id' : store_id
                    }
                }).done(function (response) {
                    $('#show_add_reply_'+cat_id+'_'+store_id).hide();
                    $('.show_add_option').show();
                    if(response.status == 1){
                       //$('#show_reply_list_'+cat_id+'_'+store_id).append('<li>'+response.data+'</li>');
                        toastr['success'](response.message);
                    }else{
                        toastr['error'](response.message);
                    }
                    window.location.reload();
                });
            });

            $(document).on('click','.save_reply_sub',function(){
                var reply = $('#reply_sub_'+cat_sub_id).val();
                if(reply == ''){
                    alert('Please enter reply');
                    return false;
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('save-sub') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'reply': reply,
                        'category_id': cat_sub_id
                    }
                }).done(function (response) {
                    $('#show_add_sub_'+cat_sub_id).hide();
                    $('.add_sub_cat').show();
                    if(response.status == 1){
                       //$('#show_reply_list_sub_'+cat_sub_id).append('<li>'+response.data+'</li>');
                        toastr['success'](response.message);
                    }else{
                        toastr['error'](response.message);
                    }
                    window.location.reload();
                });
            });

            $(document).on('click','.save_reply_sub_sub',function(){
                var reply = $('#reply_sub_sub_'+cat_sub_sub_id).val();
                if(reply == ''){
                    alert('Please enter reply');
                    return false;
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('save-sub') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'reply': reply,
                        'category_id': cat_sub_sub_id
                    }
                }).done(function (response) {
                    $('#show_add_sub_sub_'+cat_sub_sub_id).hide();
                    $('.add_sub_cat_sub').show();
                    if(response.status == 1){
                       //$('#show_reply_list_sub_'+cat_sub_id).append('<li>'+response.data+'</li>');
                        toastr['success'](response.message);
                    }else{
                        toastr['error'](response.message);
                    }
                    window.location.reload();
                });
            });

            var reply_id;
            $(document).on('click', '.edit_reply', function(){
                $('.hide_all_inputs').hide();
                $('.edit_reply').show();
                reply_id = $(this).attr('id');
                $('#'+reply_id).hide();
                $('.edit_reply_input').hide();
                $('#edit_reply_'+reply_id).show();
            });

            var sub_id;
            $(document).on('click', '.edit_reply_sub', function(){
                $('.hide_all_inputs_sub').hide();
                $('.edit_reply_sub').show();
                sub_id = $(this).attr('id');
                $('#'+sub_id).hide();
                $('.edit_reply_input_sub').hide();
                $('#edit_reply_sub_'+sub_id).show();
            });

            $(document).on('click','.update_reply',function(){
                console.log('#edit_reply_'+reply_id);
                var edit_reply = $('input[id^="edit_reply_'+reply_id+'"]').val();
                console.log(edit_reply);
                if(edit_reply == ''){
                    alert('Please enter reply');
                    return false;
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('save-store-wise-reply') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'reply_id': reply_id,
                        'reply': edit_reply
                    }
                }).done(function (response) {
                    console.log(response);
                    window.location.reload();
                });
            });

            $(document).on('click','.update_reply_sub',function(){
                console.log('#edit_reply_sub_'+sub_id);
                var edit_reply_sub = $('input[id^="edit_reply_sub_'+sub_id+'"]').val();
                console.log(edit_reply_sub);
                if(edit_reply_sub == ''){
                    alert('Please enter name');
                    return false;
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('save-sub') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'sub_id': sub_id,
                        'name': edit_reply_sub
                    }
                }).done(function (response) {
                    console.log(response);
                    window.location.reload();
                });
            });


        });
    </script>
@endsection
