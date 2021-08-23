<tr>
    <td><h3 class="text-center">1</h3></td>
    <td>
        <select class="form-control mb-2 option_menu_1" aria-label="Default select example">
        <option value="">Select</option>
        <option value="order" {{ isset($twilio_key_arr[1]['option']) && $twilio_key_arr[1]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
        <option value="product" {{ isset($twilio_key_arr[1]['option']) && $twilio_key_arr[1]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
        <option value="administration" {{ isset($twilio_key_arr[1]['option']) && $twilio_key_arr[1]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
        <option value="socialmedia" {{ isset($twilio_key_arr[1]['option']) && $twilio_key_arr[1]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
        <option value="return_refund_exchange" {{ isset($twilio_key_arr[1]['option']) && $twilio_key_arr[1]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
        <option value="general" {{ isset($twilio_key_arr[1]['option']) && $twilio_key_arr[1]['option'] == 'general' ? 'selected' : ''}}>General</option>
        </select>
    </td>   
    <td>
        <textarea class="form-control mb-2 key_description_1" rows="3">{{ isset($twilio_key_arr[1]['desc']) ? $twilio_key_arr[1]['desc'] : ''}}</textarea>
    </td>
    <td>
        <a href="#" class="btn btn-secondary save_key_option" data-id="1">Save</a>
    </td>
</tr>
<tr>
    <td><h3 class="text-center">2</h3></td>
    <td>
        <select class="form-control mb-2 option_menu_2" aria-label="Default select example">
            <option value="">Select</option>
            <option value="order" {{ isset($twilio_key_arr[2]['option']) && $twilio_key_arr[2]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
            <option value="product" {{ isset($twilio_key_arr[2]['option']) && $twilio_key_arr[2]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
            <option value="administration" {{ isset($twilio_key_arr[2]['option']) && $twilio_key_arr[2]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
        <option value="socialmedia" {{ isset($twilio_key_arr[2]['option']) && $twilio_key_arr[2]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
        <option value="return_refund_exchange" {{ isset($twilio_key_arr[2]['option']) && $twilio_key_arr[2]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
        <option value="general" {{ isset($twilio_key_arr[2]['option']) && $twilio_key_arr[2]['option'] == 'general' ? 'selected' : ''}}>General</option>
        </select>
    </td>   
    <td>
        <textarea class="form-control mb-2 key_description_2" rows="3">{{ isset($twilio_key_arr[2]['desc']) ? $twilio_key_arr[2]['desc'] : ''}}</textarea>
    </td>
    <td>
        <a href="#" class="btn btn-secondary save_key_option" data-id="2">Save</a>
    </td>
</tr>
<tr>
    <td><h3 class="text-center">3</h3></td>
    <td>
        <select class="form-control mb-2 option_menu_3" aria-label="Default select example">
            <option value="">Select</option>
            <option value="order" {{ isset($twilio_key_arr[3]['option']) && $twilio_key_arr[3]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
            <option value="product" {{ isset($twilio_key_arr[3]['option']) && $twilio_key_arr[3]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
            <option value="administration" {{ isset($twilio_key_arr[3]['option']) && $twilio_key_arr[3]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
            <option value="socialmedia" {{ isset($twilio_key_arr[3]['option']) && $twilio_key_arr[3]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
            <option value="return_refund_exchange" {{ isset($twilio_key_arr[3]['option']) && $twilio_key_arr[3]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
            <option value="general" {{ isset($twilio_key_arr[3]['option']) && $twilio_key_arr[3]['option'] == 'general' ? 'selected' : ''}}>General</option>
        </select>
    </td>   
    <td>
        <textarea class="form-control mb-2 key_description_3" rows="3">{{ isset($twilio_key_arr[3]['desc']) ? $twilio_key_arr[3]['desc'] : ''}}</textarea>
    </td>
    <td>
        <a href="#" class="btn btn-secondary save_key_option" data-id="3">Save</a>
    </td>
</tr>
<tr>
    <td><h3 class="text-center">4</h3></td>
    <td>
        <select class="form-control mb-2 option_menu_4" aria-label="Default select example">
            <option value="">Select</option>
            <option value="order" {{ isset($twilio_key_arr[4]['option']) && $twilio_key_arr[4]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
            <option value="product" {{ isset($twilio_key_arr[4]['option']) && $twilio_key_arr[4]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
            <option value="administration" {{ isset($twilio_key_arr[4]['option']) && $twilio_key_arr[4]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
            <option value="socialmedia" {{ isset($twilio_key_arr[4]['option']) && $twilio_key_arr[4]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
            <option value="return_refund_exchange" {{ isset($twilio_key_arr[4]['option']) && $twilio_key_arr[4]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
            <option value="general" {{ isset($twilio_key_arr[4]['option']) && $twilio_key_arr[4]['option'] == 'general' ? 'selected' : ''}}>General</option>
        </select>
    </td>   
    <td>
        <textarea class="form-control mb-2 key_description_4" rows="3">{{ isset($twilio_key_arr[4]['desc']) ? $twilio_key_arr[4]['desc'] : ''}}</textarea>
    </td>
    <td>
        <a href="#" class="btn btn-secondary save_key_option" data-id="4">Save</a>
    </td>
</tr>
<tr>
    <td><h3 class="text-center">5</h3></td>
    <td>
        <select class="form-control mb-2 option_menu_5" aria-label="Default select example">
            <option value="">Select</option>
            <option value="order" {{ isset($twilio_key_arr[5]['option']) && $twilio_key_arr[5]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
            <option value="product" {{ isset($twilio_key_arr[5]['option']) && $twilio_key_arr[5]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
            <option value="administration" {{ isset($twilio_key_arr[5]['option']) && $twilio_key_arr[5]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
            <option value="socialmedia" {{ isset($twilio_key_arr[5]['option']) && $twilio_key_arr[5]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
            <option value="return_refund_exchange" {{ isset($twilio_key_arr[5]['option']) && $twilio_key_arr[5]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
            <option value="general" {{ isset($twilio_key_arr[5]['option']) && $twilio_key_arr[5]['option'] == 'general' ? 'selected' : ''}}>General</option>
        </select>
    </td>   
    <td>
        <textarea class="form-control mb-2 key_description_5" rows="3">{{ isset($twilio_key_arr[5]['desc']) ? $twilio_key_arr[5]['desc'] : ''}}</textarea>
    </td>
    <td>
        <a href="#" class="btn btn-secondary save_key_option" data-id="5">Save</a>
    </td>
</tr>
<tr>
    <td><h3 class="text-center">6</h3></td>
    <td>
        <select class="form-control mb-2 option_menu_6" aria-label="Default select example">
            <option value="">Select</option>
            <option value="order" {{ isset($twilio_key_arr[6]['option']) && $twilio_key_arr[6]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
            <option value="product" {{ isset($twilio_key_arr[6]['option']) && $twilio_key_arr[6]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
            <option value="administration" {{ isset($twilio_key_arr[6]['option']) && $twilio_key_arr[6]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
            <option value="socialmedia" {{ isset($twilio_key_arr[6]['option']) && $twilio_key_arr[6]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
            <option value="return_refund_exchange" {{ isset($twilio_key_arr[6]['option']) && $twilio_key_arr[6]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
            <option value="general" {{ isset($twilio_key_arr[6]['option']) && $twilio_key_arr[6]['option'] == 'general' ? 'selected' : ''}}>General</option>
        </select>
    </td>   
    <td>
        <textarea class="form-control mb-2 key_description_6" rows="3">{{ isset($twilio_key_arr[6]['desc']) ? $twilio_key_arr[6]['desc'] : ''}}</textarea>
    </td>
    <td>
        <a href="#" class="btn btn-secondary save_key_option" data-id="6">Save</a>
    </td>
</tr>
<tr>
    <td><h3 class="text-center">7</h3></td>
    <td>
        <select class="form-control mb-2 option_menu_7" aria-label="Default select example">
            <option value="">Select</option>
            <option value="order" {{ isset($twilio_key_arr[7]['option']) && $twilio_key_arr[7]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
            <option value="product" {{ isset($twilio_key_arr[7]['option']) && $twilio_key_arr[7]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
            <option value="administration" {{ isset($twilio_key_arr[7]['option']) && $twilio_key_arr[7]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
            <option value="socialmedia" {{ isset($twilio_key_arr[7]['option']) && $twilio_key_arr[7]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
            <option value="return_refund_exchange" {{ isset($twilio_key_arr[7]['option']) && $twilio_key_arr[7]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
            <option value="general" {{ isset($twilio_key_arr[7]['option']) && $twilio_key_arr[7]['option'] == 'general' ? 'selected' : ''}}>General</option>
        </select>
    </td>   
    <td>
        <textarea class="form-control mb-2 key_description_7" rows="3">{{ isset($twilio_key_arr[7]['desc']) ? $twilio_key_arr[7]['desc'] : ''}}</textarea>
    </td>
    <td>
        <a href="#" class="btn btn-secondary save_key_option" data-id="7">Save</a>
    </td>
</tr>
<tr>
    <td><h3 class="text-center">8</h3></td>
    <td>
        <select class="form-control mb-2 option_menu_8" aria-label="Default select example">
            <option value="">Select</option>
            <option value="order" {{ isset($twilio_key_arr[8]['option']) && $twilio_key_arr[8]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
            <option value="product" {{ isset($twilio_key_arr[8]['option']) && $twilio_key_arr[8]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
            <option value="administration" {{ isset($twilio_key_arr[8]['option']) && $twilio_key_arr[8]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
            <option value="socialmedia" {{ isset($twilio_key_arr[8]['option']) && $twilio_key_arr[8]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
            <option value="return_refund_exchange" {{ isset($twilio_key_arr[8]['option']) && $twilio_key_arr[8]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
            <option value="general" {{ isset($twilio_key_arr[8]['option']) && $twilio_key_arr[8]['option'] == 'general' ? 'selected' : ''}}>General</option>
        </select>
    </td>   
    <td>
        <textarea class="form-control mb-2 key_description_8" rows="3">{{ isset($twilio_key_arr[8]['desc']) ? $twilio_key_arr[8]['desc'] : ''}}</textarea>
    </td>
    <td>
        <a href="#" class="btn btn-secondary save_key_option" data-id="8">Save</a>
    </td>
</tr>
<tr>
    <td><h3 class="text-center">9</h3></td>
    <td>
        <select class="form-control mb-2 option_menu_9" aria-label="Default select example">
            <option value="">Select</option>
            <option value="order" {{ isset($twilio_key_arr[9]['option']) && $twilio_key_arr[9]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
            <option value="product" {{ isset($twilio_key_arr[9]['option']) && $twilio_key_arr[9]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
            <option value="administration" {{ isset($twilio_key_arr[9]['option']) && $twilio_key_arr[9]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
            <option value="socialmedia" {{ isset($twilio_key_arr[9]['option']) && $twilio_key_arr[9]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
            <option value="return_refund_exchange" {{ isset($twilio_key_arr[9]['option']) && $twilio_key_arr[9]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
            <option value="general" {{ isset($twilio_key_arr[9]['option']) && $twilio_key_arr[9]['option'] == 'general' ? 'selected' : ''}}>General</option>
        </select>
    </td>   
    <td>
        <textarea class="form-control mb-2 key_description_9" rows="3">{{ isset($twilio_key_arr[9]['desc']) ? $twilio_key_arr[9]['desc'] : ''}}</textarea>
    </td>
    <td>
        <a href="#" class="btn btn-secondary save_key_option" data-id="9">Save</a>
    </td>
</tr>



<script type="text/javascript">
 $('.save_key_option').on("click", function(e){
    var key_no = $(this).data("id");
    var option = $('.option_menu_'+key_no).val();
    var desc = $('.key_description_'+key_no).val();
    var website_id = $('.store_website_twilio_key').val();

    if(option == '')
    {
        toastr['error']('Please select Option');
        return false;
    }
    if(desc == '')
    {
        toastr['error']('Please Enter Description');
        return false;
    }

    $.ajax({
        type: "POST",
        url: "{{ route('twilio.set_twilio_key_options') }}",  
        data: {
            _token: "{{csrf_token()}}",
            key_no:key_no,
            option:option,
            description:desc,
            website_store_id:website_id,
        },
        beforeSend : function() {
            
        },
        success: function (response) {
            if(response.status == 1){
                toastr['success'](response.message);
            }else if(response.status == 0){
                toastr['error'](response.message);
            }
        },
        error: function (response) { 
            
        }
    }); 
});
</script>