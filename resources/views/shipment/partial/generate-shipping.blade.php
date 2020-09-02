<div id="addShipment" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content ">
            <form action="javascript:void(0)" id="generate-shipment-form" method="POST">
                <input type="hidden" name="order_id" value="{{ isset($id) ? $id : '' }}">
                <div class="modal-header">
                    <h4 class="modal-title">Generate AWB</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-2 any-message">

                    </div>
                    <div class="form-group mb-2">
                        <strong>Customer Name:</strong>
                        <select class="form-control" name="customer_id" id="customer_name">
                            <option value="">Select Customer</option>
                            @if(isset($customers))
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <span class="form-error"></span>
                    </div>
                    <div class="form-group mb-2">
                        <strong>Customer City:</strong>
                        <input type="text" name="customer_city" class="form-control input_customer_city" value="" >
                        <span class="form-error"></span>
                    </div>
                    <div class="form-group mb-2">
                        <strong>Customer Country (ISO 2):</strong>
                        <select name="customer_country" style="text-transform: capitalize" class="form-control input_customer_country">
                            <option value="" selected>Select Country</option>
                            @if(isset($countries) && count($countries))
                                @foreach($countries as $key=>$country)
                                    <option value="{{$key}}">{{ ucfirst(strtolower($country['name'])) }}</option>
                                @endforeach
                            @endif
                        </select>
                        <span class="form-error"></span>
                    </div>
                    <div class="form-group mb-2">
                        <strong>Customer Phone:</strong>
                        <input type="number" name="customer_phone" class="form-control input_customer_phone" >
                        <span class="form-error"></span>

                    </div>

                    <div class="form-group mb-2">
                        <strong>Customer Address 1:</strong>
                        <input type="text" name="customer_address1" class="form-control input_customer_address1" >
                        <span class="form-error"></span>

                    </div>

                    <div class="form-group mb-2">
                        <strong>Customer Address 2:</strong>
                        <input type="text" name="customer_address2" class="form-control input_customer_address2" >
                        <span class="form-error"></span>

                    </div>

                    <div class="form-group mb-2">
                        <strong>Customer Pincode:</strong>
                        <input type="number" name="customer_pincode" class="form-control input_customer_pincode" max="999999" >
                        <span class="form-error"></span>

                    </div>

                    <div class="form-group mb-2">
                        <strong>Actual Weight:</strong>
                        <input type="number" name="actual_weight" class="form-control input_actual_weight" value="1" step="0.01" >
                        <span class="form-error"></span>

                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="form-group mb-2">
                                <strong>Length:</strong>
                                <input type="number" name="box_length" class="form-control input_box_length" placeholder="1.0" value="" step="0.1" max="1000" >
                                <span class="form-error"></span>

                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group mb-2">
                                <strong>Width:</strong>
                                <input type="number" name="box_width" class="form-control input_box_width" placeholder="1.0" value="" step="0.1" max="1000" >
                                <span class="form-error"></span>

                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group mb-2">
                                <strong>Height:</strong>
                                <input type="number" name="box_height" class="form-control input_box_height" placeholder="1.0" value="" step="0.1" max="1000" >
                                <span class="form-error"></span>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group mb-2">
                                <strong>Amount:</strong>
                                <input type="number" name="amount" class="form-control input_amount" value="" >
                                <span class="form-error"></span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group mb-2">
                                <strong>Currency:</strong>
                                <select name="currency" class="form-control input_currency">
                                    <option selected>USD</option>
                                    <option>GBP</option>
                                    <option>EURO</option>
                                    <option>AED</option>
                                    <option>JPY</option>
                                    <option>CNY</option>
                                </select>
                                <span class="form-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group mb-2">
                                <strong>Pick Up Date and Time</strong>
                                <div class='input-group date' id='pickup-datetime'>
                                    <input type='text' class="form-control input_pickup_time" name="pickup_time" value="{{ date('Y-m-d H:i') }}"  />
                                    <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <span class="form-error"></span>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col price-break-down">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <button type="submit" style="margin-top: 5px;" class="btn btn-secondary btn-create-shipment-request">
                            <i class="fa fa-spinner fa-spin"></i>Create Shipment on DHL</button>
                        {{--<button type="button" style="margin-top: 5px;" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" style="margin-top: 5px;" class="btn btn-secondary btn-rate-request">Calculate Rate Request</button>
                        <button type="submit" style="margin-top: 5px;" class="btn btn-secondary">Update and Generate</button>--}}
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let errorFields = $('#generate-shipment-form .form-error'),
        submitButton = $('.btn-create-shipment-request')[0],
        loaderField = $('.btn-create-shipment-request .fa'),
        anyMessageField = $('.any-message');

    const sendMessages = (e,self) => {
        if (!e.success && e.errors){
            for (let i in e.errors) if (e.errors.hasOwnProperty(i)) {
                if (i === 'order_id') continue
                self.find(`[name=${i}]`).parents('.form-group').find('.form-error').show().text(e.errors[i][0]);
            }
        }else if(!e.success && e.globalErrors){
            let html = '';
            if (typeof e.globalErrors === 'string'){
                html = `<div class="col-lg-12 alert alert-danger">${e.globalErrors}</div>`;
            }else{
                html = '<div class="col-lg-12 alert alert-danger"><ul>';

                for (let i in e.globalErrors) if (e.globalErrors.hasOwnProperty(i)) {
                    html += `<li>${e.globalErrors[i]}</li>`
                }
                html += '</ul></div>'
            }

            anyMessageField.html(html)
        }
        else if(e.success){
            anyMessageField.html('<div class="alert alert-success" role="alert">Shipment created successfully</div>')
        }else{
            let html = `<div class="col-lg-12 alert alert-danger">Something get wrong please try again!</div>`
            anyMessageField.html(html)
        }
    }

    $('#generate-shipment-form').on('submit', function(e){
        let self = ($(this)),
            formData = {};
        self.serializeArray().forEach(x => formData[x.name] = x.value);
        errorFields.html('');
        submitButton.disabled = true
        anyMessageField.html('')
        loaderField.show()
        $.ajax({
            url: '{{ route('shipment/generate') }}',
            method: 'post',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            data: formData,
            success: function(e){
                submitButton.disabled = false
                $('#addShipment').animate({scrollTop: 0}, 'slow')
                sendMessages(e,self)
                loaderField.hide()
            },
            error: function(e){
                $('#addShipment').animate({scrollTop: 0}, 'slow')
                submitButton.disabled = false
                let html = `<div class="col-lg-12 alert alert-danger">Something get wrong please try again!</div>`
                anyMessageField.html(html)
            }
        });
    })
</script>