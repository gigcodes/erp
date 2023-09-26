

<div id="magento-commands-modal" class="modal fade" role="dialog">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<style>
    .multiselect {
        width: 100%;
    }

    .multiselect-container li a {
        line-height: 3;
    }

    /* Pagination style */
    .pagination>li>a,
    .pagination>li>span {
        color: #343a40!important // use your own color here
    }

    .pagination>.active>a,
    .pagination>.active>a:focus,
    .pagination>.active>a:hover,
    .pagination>.active>span,
    .pagination>.active>span:focus,
    .pagination>.active>span:hover {
        background-color: #343a40 !important;
        border-color: #343a40 !important;
        color: white !important
    }
    .select2-search--inline {
    display: contents; /*this will make the container disappear, making the child the one who sets the width of the element*/
}

.select2-search__field {
    width: 100% !important; /*makes the placeholder to be 100% of the width while there are no options selected*/
}

</style>


    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Magento Commands
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="width:auto;height:auto;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="mt-3 col-md-12">
                <form class="form-inline" action="" method="GET" id="magento-command-date-form">
                    {{ csrf_field() }}
                    <div class="col">
                        <div class="form-group">
                            <div class="input-group">
                                <select name="website[]" class="form-control select2" data-placeholder="Select Websites" id="website" multiple>
                                    <option></option>
                                    <option value="ERP" @if(!empty(request('website')) && in_array('ERP',request('website'))) selected @endif>ERP</option>
                                    <?php
                                  $ops = 'id';
                                ?>
                                    @foreach($websites_filter as $website)
                                        <option @if(!empty(request('website')) && in_array($website->id ,request('website'))) selected @endif value="{{$website->id}}">{{$website->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <div class="input-group">
                                <select name="command_name[]" class="form-control select2" id="command_name" multiple data-placeholder="Select Command Name">
                                    <option></option>
                                    @foreach ($magentoCommandListArray as $comName => $comType)
                                    <option @if(!empty(request('command_name')) && in_array($comName ,request('command_name'))) selected @endif value="{{$comName}}">{{$comName}}</option>
                                    @endforeach
                                </select>
                                {{-- <input type="text" placeholder="Request Name" class="form-control" name="request_name" value="{{request('request_name')}}"> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <div class="input-group">
                                <select name="user_id[]" class="form-control select2" id="user_id" multiple data-placeholder="Select User Name">
                                    <option></option>
                                    @foreach ($users_filter as $key => $user)
                                    <option @if(!empty(request('user_id')) &&  in_array($user->id ,request('user_id'))) selected @endif value="{{$user->id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                                {{-- <input type="text" placeholder="Request Name" class="form-control" name="request_name" value="{{request('request_name')}}"> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
                    </div>
                </form>
                
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12" id="magento-commands-modal-html">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
