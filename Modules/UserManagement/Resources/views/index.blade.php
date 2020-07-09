@extends('layouts.app')
@section('favicon' , 'task.png')


@section('content')
<style type="text/css">
    .preview-category input.form-control {
      width: auto;
    }
</style>

<style>
    #payment-table_filter {
        text-align: right;
    }

    .activity-container {
        margin-top: 3px;
    }

    .elastic {
        transition: height 0.5s;
    }

    .activity-table-wrapper {
        position: absolute;
        width: calc(100% - 50px);
        max-height: 500px;
        overflow-y: auto;
    }

    .dropdown-wrapper {
        position: relative;
    }

    .dropdown-wrapper.hidden {
        display: none;
    }

    .dropdown-wrapper>ul {
        margin: 0px;
        padding: 5px;
        list-style: none;
        position: absolute;
        width: 100%;
        box-shadow: 3px 3px 10px 0px;
        background: white;
    }

    .dropdown input {
        width: calc(100% - 120px);
        line-height: 2;
        outline: none;
        border: none;
    }

    .payment-method-option:hover {
        background: #d4d4d4;
    }

    .payment-method-option.selected {
        font-weight: bold;
    }

    .payment-dropdown-header {
        padding: 2px;
        border: 1px solid #e0e0e0;
        border-radius: 3px;
    }

    .payment-overlay {
        position: absolute;
        height: 100%;
        width: 100%;
        top: 0px;
    }

    .error {
        color: red;
        font-size: 10pt;
    }
</style>
@include('partials.flash_messages')
<div class="row" id="common-page-layout">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">
        <div class="row">
            <div class="col">
                <div class="h" style="margin-bottom:10px;">
                    <div class="row">
                        <form class="form-inline message-search-handler" method="post">
                            <div class="col">
                                <div class="form-group">
                                    <label for="keyword">Keyword:</label>
                                    <?php echo Form::text("keyword",request("keyword"),["class"=> "form-control","placeholder" => "Enter keyword"]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="keyword">Active:</label>
                                    <select name="is_active"  class="form-control">
                                        <option value="0" {{request("is_active") == 0 ? 'selected' : ''}}>All</option>
                                        <option value="1" {{request("is_active") == 1 ? 'selected' : ''}}>Active</option>
                                        <option value="2" {{request("is_active") == 2 ? 'selected' : ''}}>In active</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="button">&nbsp;</label>
                                    <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
                                        <img src="/images/search.png" style="cursor: default;">
                                    </button>
                                </div>      
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>  
        <div class="col-md-12 margin-tb" id="page-view-result">

        </div>
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal modal-md" role="dialog">
    <div class="modal-dialog" role="document" id="modalDialog">
    </div>  
</div>

@include("usermanagement::templates.list-template")
@include("usermanagement::templates.create-solution-template")
@include("usermanagement::templates.load-communication-history")
@include("usermanagement::templates.add-role")
@include("usermanagement::templates.add-permission")


<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="/js/common-helper.js"></script>
<script type="text/javascript" src="/js/user-management-list.js"></script>


<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script type="text/javascript">
    page.init({
        bodyView : $("#common-page-layout"),
        baseUrl : "<?php echo url("/"); ?>"
    });


    function editUser(id) {
       $.ajax({
          url: "/user-management/edit/"+id,
          type: "get"
        }).done(function(response) {
          $('.common-modal').modal('show');
          console.log($(".modal-dialog"));
           $(".modal-dialog").html(response); 
        }).fail(function(errObj) {
            $('.common-modal').modal('hide');
        });
    }

    function payuser(id) {
       $.ajax({
          url: "/user-management/paymentInfo/"+id,
          type: "get"
        }).done(function(response) {
          $('.common-modal').modal('show');
          console.log($(".modal-dialog"));
           $(".modal-dialog").html(response); 
        }).fail(function(errObj) {
            $('.common-modal').modal('hide');
        });
    }

    $(".common-modal").on("click",".open-payment-method",function() {
        if ($('.common-modal #permission-from').hasClass('hidden')) {
            $('.common-modal #permission-from').removeClass('hidden');
        } else {
            $('.common-modal #permission-from').addClass('hidden');
        }
        });

        $(".common-modal").on("click",".add-payment-method",function() {
            var name = $('.common-modal #payment-method-input').val();
            console.log(name);
            if(!name) {
                return;
            }

            $.ajax({
            url: "/user-management/add-new-method",
            type: "post",
            data: {
                name : name,
                "_token": "{{ csrf_token() }}"
            }
            }).done(function(response) {
            $(".common-modal #payment_method").html(response); 
            $('.common-modal #permission-from').addClass('hidden');
            $('.common-modal #payment-method-input').val('');
            }).fail(function(errObj) {
            });
        });

        


    let paymentMethods;

    function makePayment(userId, defaultMethod = null) {
        $('input[name="user_id"]').val(userId);

        if (defaultMethod) {
            $('#payment_method').val(defaultMethod);
        }
        filterMethods('');
        $('.dropdown input').val('');

        $("#paymentModal").modal();
    }

    function setPaymentMethods() {
        paymentMethods = $('.payment-method-option');
        console.log(paymentMethods);
    }

    $(document).ready(function() {

        adjustHeight();

        $('#payment-table').DataTable({
            "ordering": true,
            "info": false
        });

        setPaymentMethods();

        $('#payment-dropdown-wrapper').click(function() {
            event.stopPropagation();
        })

        $("#paymentModal").click(function() {
            closeDropdown();
        })
    });

    function adjustHeight() {
        $('.activity-container').each(function(index, element) {
            const childElement = $($(element).children()[0]);
            $(element).attr('data-expanded-height', childElement.height());
            $(element).height(0);
            childElement.height(0);

            setTimeout(
                function() {
                    $(element).addClass('elastic');
                    childElement.addClass('elastic');
                    $('#payment-table').css('visibility', 'visible');
                },
                1
            )
        })
    }

    function toggle(id) {
        const expandableElement = $('#elastic-' + id);

        const isExpanded = expandableElement.attr('data-expanded') === 'true';


        if (isExpanded) {
            console.log('true1');
            expandableElement.height(0);
            $($(expandableElement).children()[0]).height(0);
            expandableElement.attr('data-expanded', 'false');
        } else {
            console.log('false1');
            const expandedHeight = expandableElement.attr('data-expanded-height');
            expandableElement.height(expandedHeight);
            $($(expandableElement).children()[0]).height(expandedHeight);
            expandableElement.attr('data-expanded', 'true');
        }



    }

  

    function filterMethods(needle) {
        console.log(needle);
        $('#payment-method-dropdown .payment-method-option').remove();

        let filteredElements = paymentMethods.filter(
            function(index, element) {
                const optionValue = $(element).text();
                return optionValue.toLowerCase().includes(needle.toLowerCase());
            }
        )

        filteredElements.each(function(index, element) {
            const value = $(element).text();
            if (value == $('#payment_method').val()) {
                $(element).addClass('selected');
            } else {
                $(element).removeClass('selected');
            }
        });

        $('#payment-method-dropdown').append(filteredElements);
    }

    function selectOption(element) {
        selectOptionWithText($(element).text());
    }

    function selectOptionWithText(text) {
        $('#payment_method').val(text);
        closeDropdown();
    }


    function toggleDropdown() {
        if ($('#payment-dropdown-wrapper').hasClass('hidden')) {
            filterMethods('');
            $('.dropdown input').val('');
            $('#payment-dropdown-wrapper').css('display','block !important');
            $('#payment-dropdown-wrapper').removeClass('hidden');
        } else {
            $('#payment-dropdown-wrapper').addClass('hidden');
        }
        event.stopPropagation();
    }

    function closeDropdown() {
        $('#payment-dropdown-wrapper').addClass('hidden');
    }

    function addPaymentMethod() {

        console.log('here');

        const newPaymentMethod = $('#payment-method-input').val();

        let paymentExists = false;
        $('#payment-method-dropdown .payment-method-option')
            .each(function(index, element) {
                if ($(element).text() == newPaymentMethod) {
                    paymentExists = true;
                }
            });

        if (paymentExists) {
            alert('Payment method exits');
            return;
        } else if (!newPaymentMethod || newPaymentMethod.trim() == '') {
            alert('Payment method required');
            return;
        }

        filterMethods('');

        $('#payment-method-dropdown').append(
            '<li onclick="selectOption(this)" class="payment-method-option">' + newPaymentMethod + '</li>'
        );

        $('#payment_method').append(
            '<option value="' + newPaymentMethod + '">' + newPaymentMethod + '</option>'
        );

        setPaymentMethods();



        selectOptionWithText(newPaymentMethod);
        event.stopPropagation();
        event.preventDefault();

        return true;
    }
</script>

@endsection
